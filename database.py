import psycopg2
from psycopg2 import sql
import os
from dotenv import load_dotenv
from sshtunnel import SSHTunnelForwarder

# Load environment variables FIRST
load_dotenv()

# Global variable to hold SSH tunnel
ssh_tunnel = None

def get_db_config():
    """Get database configuration from environment variables or use defaults"""
    return {
        'dbname': os.getenv('DB_NAME', ''),
        'user': os.getenv('DB_USER', ''),
        'password': os.getenv('DB_PASSWORD', ''),
        'host': os.getenv('DB_HOST', ''),
        'port': os.getenv('DB_PORT', '5432')
    }

def get_ssh_config():
    """Get SSH tunnel configuration from environment variables"""
    return {
        'use_ssh': os.getenv('USE_SSH_TUNNEL', 'false').lower() == 'true',
        'ssh_host': os.getenv('SSH_HOST'),
        'ssh_port': int(os.getenv('SSH_PORT', '22')),
        'ssh_user': os.getenv('SSH_USER'),
        'ssh_password': os.getenv('SSH_PASSWORD'),
        'remote_bind_host': os.getenv('REMOTE_BIND_HOST', 'localhost'),
        'remote_bind_port': int(os.getenv('REMOTE_BIND_PORT', '5432'))
    }

def get_sound_folder_config():
    """Get sound folder configuration from environment variables or use defaults"""
    return os.getenv('SOUND_FOLDER', 'sound_files')

def setup_ssh_tunnel():
    """Setup SSH tunnel if configured"""
    global ssh_tunnel
    ssh_config = get_ssh_config()
    
    if not ssh_config['use_ssh']:
        print("SSH tunnel disabled")
        return None
    
    print(f"Setting up SSH tunnel to {ssh_config['ssh_host']}:{ssh_config['ssh_port']}...")
    
    ssh_tunnel = SSHTunnelForwarder(
        (ssh_config['ssh_host'], ssh_config['ssh_port']),
        ssh_username=ssh_config['ssh_user'],
        ssh_password=ssh_config['ssh_password'],
        remote_bind_address=(ssh_config['remote_bind_host'], ssh_config['remote_bind_port']),
        allow_agent=False,
        host_pkey_directories=None
    )
    
    ssh_tunnel.start()
    print(f"SSH tunnel established. Local port: {ssh_tunnel.local_bind_port}")
    return ssh_tunnel

def get_db_connection():
    """Create a connection to PostgreSQL database (through SSH tunnel if configured)"""
    config = get_db_config()
    
    # If SSH tunnel is active, use the local port
    if ssh_tunnel and ssh_tunnel.is_active:
        config['host'] = '127.0.0.1'
        config['port'] = str(ssh_tunnel.local_bind_port)
        print(f"Connecting through SSH tunnel to {config['host']}:{config['port']}")
    else:
        print(f"Connecting directly to {config['host']}:{config['port']}")
    
    conn = psycopg2.connect(**config)
    return conn

def init_db():
    """Initialize the database with a questionnaire responses table"""
    config = get_db_config()
    
    try:
        # Connect to PostgreSQL
        print(f"Database: {config['dbname']}, User: {config['user']}")
        conn = get_db_connection()
        conn.autocommit = True
        cursor = conn.cursor()
        
        # Try to create database if it doesn't exist
        cursor.execute("SELECT 1 FROM pg_catalog.pg_database WHERE datname = %s", (config['dbname'],))
        exists = cursor.fetchone()
        if not exists:
            cursor.execute(sql.SQL("CREATE DATABASE {}").format(sql.Identifier(config['dbname'])))
            print(f"Database '{config['dbname']}' created successfully!")
        else:
            print(f"Database '{config['dbname']}' already exists.")
        
        # Create the submissions table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS submissions (
                user_code VARCHAR(50) PRIMARY KEY,
                age INTEGER NOT NULL CHECK (age > 0 AND age <= 120),
                gender VARCHAR(50) NOT NULL,
                highest_education VARCHAR(100) NOT NULL,
                submitted_before BOOLEAN NOT NULL,
                feedback TEXT,
                timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        print("Created 'submissions' table.")

        # Create the sound_samples table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS sound_samples (
                sound_code VARCHAR(50) NOT NULL,
                result_num NUMERIC NOT NULL DEFAULT 0,
                PRIMARY KEY (sound_code)
            )
        ''')
        
        print("Created 'sound_samples' table.")
        
        # Create the results table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS results (
                id SERIAL PRIMARY KEY,
                user_code VARCHAR(50) NOT NULL,
                sound_code VARCHAR(50) NOT NULL,
                emotion1 VARCHAR(50) NOT NULL,
                rating1 NUMERIC(5,2) NOT NULL,
                emotion2 VARCHAR(50),
                rating2 NUMERIC(5,2),
                FOREIGN KEY (user_code) REFERENCES submissions(user_code) ON DELETE CASCADE,
                FOREIGN KEY (sound_code) REFERENCES sound_samples(sound_code) ON DELETE CASCADE
            )
        ''')

        print("Created 'results' table.")
        
        # Create indexes for better query performance
        try:
            cursor.execute('''
                CREATE INDEX idx_submissions_timestamp 
                ON submissions(timestamp DESC)
            ''')
            print("Created index: idx_submissions_timestamp")
        except psycopg2.errors.DuplicateTable:
            print("Index idx_submissions_timestamp already exists")

        try:
            cursor.execute('''
                CREATE INDEX idx_results_code 
                ON results(sound_code)
            ''')
            print("Created index: idx_results_code")
        except psycopg2.errors.DuplicateTable:
            print("Index idx_results_code already exists")

        conn.commit()
        cursor.close()
        conn.close()

        # Populate sound_samples table
        populate_sound_samples()
        
        print("Database tables initialized successfully!")
        
    except psycopg2.Error as e:
        print(f"Database error: {e}")
        raise
    except Exception as e:
        print(f"Error: {e}")
        raise

def populate_sound_samples():
    """
    Scan sound files and add them to sound_samples table if not already present.
    Sets result_num to DEFAULT 0 for new entries.
    """
    project_root = os.path.dirname(os.path.abspath(__file__))
    sounds_dir = os.path.join(project_root, 'static', get_sound_folder_config())
    
    # Check if sounds directory exists
    if not os.path.exists(sounds_dir):
        print(f"Error: Sounds directory not found at {sounds_dir}")
        print(f"Please create static/{get_sound_folder_config()}/ folder and add .wav files.")
        return
    
    # Get all .wav files
    sound_files = [f for f in os.listdir(sounds_dir) if f.endswith('.wav')]
    
    if not sound_files:
        print(f"No .wav files found in {sounds_dir}")
        return
    
    # Sort files alphabetically
    sound_files.sort()
    
    print(f"Found {len(sound_files)} sound file(s)")
    
    # Connect to database
    conn = get_db_connection()
    conn.autocommit = True
    cursor = conn.cursor()
    
    added_count = 0
    skipped_count = 0
    
    for filename in sound_files:
        # Remove .wav extension to get sound_code
        sound_code = filename.replace('.wav', '')
        
        try:
            # Check if sound_code already exists
            cursor.execute(
                "SELECT COUNT(*) FROM sound_samples WHERE sound_code = %s",
                (sound_code,)
            )
            exists = cursor.fetchone()[0] > 0
            
            if exists:
                print(f"  Skipped (already exists): {sound_code}")
                skipped_count += 1
            else:
                # Insert new sound_code with result_num = 0
                cursor.execute(
                    "INSERT INTO sound_samples (sound_code) VALUES (%s)",
                    (sound_code,)
                )
                conn.commit()
                print(f"  Added: {sound_code}")
                added_count += 1
                
        except Exception as e:
            print(f"  Error processing {sound_code}: {e}")
            conn.rollback()
    
    # Close database connection
    conn.commit()
    cursor.close()
    conn.close()

    conn.close()
    
    print(f"\nSummary:")
    print(f"  Added: {added_count}")
    print(f"  Skipped: {skipped_count}")
    print(f"  Total: {len(sound_files)}")

if __name__ == '__main__':
    try:
        # Setup SSH tunnel if needed
        setup_ssh_tunnel()
        
        # Initialize database
        init_db()
    finally:
        # Close SSH tunnel when done
        if ssh_tunnel:
            print("Closing SSH tunnel...")
            ssh_tunnel.stop()
            print("SSH tunnel closed.")