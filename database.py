import psycopg2
from psycopg2 import sql
import os
from dotenv import load_dotenv

def get_db_config():
    """Get database configuration from environment variables or use defaults"""
    load_dotenv()
    return {
        'dbname': os.getenv('DB_NAME', 'sound_test_db'),
        'user': os.getenv('DB_USER', 'postgres'),
        'password': os.getenv('DB_PASSWORD', 'postgres'),
        'host': os.getenv('DB_HOST', 'localhost'),
        'port': os.getenv('DB_PORT', '5432')
    }

def init_db():
    """Initialize the database with a questionnaire responses table"""
    config = get_db_config()
    
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
        
        cursor.close()
        conn.close()
        
        # Connect to the questionnaire database
        conn = psycopg2.connect(**config)
        cursor = conn.cursor()
        
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
        
        print("Database tables initialized successfully!")
        
    except psycopg2.Error as e:
        print(f"Database error: {e}")
        raise
    except Exception as e:
        print(f"Error: {e}")
        raise

if __name__ == '__main__':
    init_db()