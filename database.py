import psycopg2
from psycopg2 import sql
import os
from dotenv import load_dotenv

def get_config():
    """Get database configuration from environment variables or use defaults"""
    load_dotenv()
    return {
        'dbname': os.getenv('DB_NAME', 'sound_test_db'),
        'user': os.getenv('DB_USER', 'postgres'),
        'password': os.getenv('DB_PASSWORD', 'postgres'),
        'host': os.getenv('DB_HOST', 'localhost'),
        'port': os.getenv('DB_PORT', '5432'),
        'soundfolder': os.getenv('SOUND_FOLDER', 'sound_files')
    }

def init_db():
    """Initialize the database with a questionnaire responses table"""
    config = get_config()
    
    try:
        # Connect to PostgreSQL server (to default 'postgres' database first)
        print(config)
        conn = psycopg2.connect(
            dbname=config['dbname'],
            user=config['user'],
            password=config['password'],
            host=config['host'],
            port=config['port']
        )
        conn.autocommit = True
        cursor = conn.cursor()
        
        # Create database if it doesn't exist
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

        cursor.execute('''
            CREATE TABLE IF NOT EXISTS sound_samples (
                sound_code VARCHAR(50) NOT NULL,
                result_num NUMERIC NOT NULL DEFAULT 0,
                PRIMARY KEY (sound_code)
            )
        ''')
        
        print("Created 'sound_samples' table.")
        
        # Create indexes for better query performance
        cursor.execute('''
            CREATE INDEX IF NOT EXISTS idx_submissions_timestamp 
            ON submissions(timestamp DESC)
        ''')
        
        cursor.execute('''
            CREATE INDEX IF NOT EXISTS idx_results_code 
            ON results(sound_code)
        ''')

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
    
    Args:
        static_folder: Path to your static folder
    """
    config = get_config()
    project_root = os.path.dirname(os.path.abspath(__file__))
    sounds_dir = os.path.join(project_root, 'static', config['soundfolder'])
    
    # Check if sounds directory exists
    if not os.path.exists(sounds_dir):
        print(f"Error: Sounds directory not found at {sounds_dir}")
        print("Please create static/sounds/ folder and add .wav files.")
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
    conn = psycopg2.connect(
        dbname=config['dbname'],
        user=config['user'],
        password=config['password'],
        host=config['host'],
        port=config['port']
    )
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
    init_db()