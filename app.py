from flask import Flask, render_template, request, jsonify, url_for
import psycopg2
from psycopg2.extras import RealDictCursor
import os
from datetime import datetime
from dotenv import load_dotenv
import secrets
import string

app = Flask(__name__)

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

def get_sound_folder_config():
    """Get sound folder configuration from environment variables or use defaults"""
    load_dotenv()
    return os.getenv('SOUND_FOLDER', 'sound_files')

def get_db_connection():
    """Create a connection to PostgreSQL database"""
    config = get_db_config()
    conn = psycopg2.connect(**config)
    return conn

def generate_unique_code(length=10):
    """Generate a unique alphanumeric code"""
    characters = string.ascii_uppercase + string.digits
    return ''.join(secrets.choice(characters) for _ in range(length))

@app.route('/')
def index():
    return render_template('sound_test.html')

@app.route('/get-sounds')
def get_sounds():
    """Return list of available sound files with full paths"""
    count = request.args.get('count', type=str, default=10)
    
    sounds_dir = os.path.join(app.static_folder, get_sound_folder_config())
    
    # Check if sounds directory exists
    if not os.path.exists(sounds_dir):
        return jsonify({
            'success': False,
            'message': f'Sounds directory not found. Please create {sounds_dir} folder and add .wav files.'
        })
    
    # Get all .wav files
    sound_files = [f for f in os.listdir(sounds_dir) if f.endswith('.wav')]
    
    if not sound_files:
        return jsonify({
            'success': False,
            'message': 'No .wav files found in the sounds directory.'
        })
    
    # Get sound samples' submission counts from database
    conn = get_db_connection()
    cursor = conn.cursor()

    sound_codes = [os.path.splitext(os.path.basename(path))[0] for path in sound_files]

    # Prepare query to preserve order by result_num
    format_strings = ','.join(['%s'] * len(sound_codes))
    query = f"""
        SELECT sound_code, result_num
        FROM sound_samples
        WHERE sound_code IN ({format_strings})
        ORDER BY result_num ASC
    """
    cursor.execute(query, tuple(sound_codes))
    rows = cursor.fetchall()

    # Create a mapping from sound_code to full file path for lookup
    code_to_path = {os.path.splitext(os.path.basename(path))[0]: path for path in sound_files}

    # Now, produce the sorted list of file paths according to result_num order
    ordered_sound_files = [code_to_path[row[0]] for row in rows]
    
    # Limit to requested count
    try:
        limit = int(count)
        ordered_sound_files = ordered_sound_files[:limit]
    except ValueError:
        return jsonify({
            'success': False,
            'message': 'Count is not convertible to int.'
        })
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()
    
    # Generate full URLs for each sound file
    sound_urls = [url_for('static', filename=f'{get_sound_folder_config()}/{filename}') for filename in ordered_sound_files]
    
    return jsonify({
        'success': True,
        'sounds': sound_urls,
        'count': len(sound_urls)
    })

@app.route('/submit', methods=['POST'])
def submit_questionnaire():
    conn = None
    cursor = None
    
    try:
        data = request.get_json()
        
        # Generate unique user_code
        user_code = generate_unique_code()
        
        conn = get_db_connection()
        cursor = conn.cursor()
        
        # Insert into submissions table
        cursor.execute('''
            INSERT INTO submissions 
            (user_code, age, gender, highest_education, submitted_before, feedback)
            VALUES (%s, %s, %s, %s, %s, %s)
        ''', (
            user_code,
            int(data.get('age')),
            data.get('gender'),
            data.get('highest_education'),
            data.get('submitted_before', False),
            data.get('feedback')
        ))
        
        # Insert multiple rows into results table
        results = data.get('results', [])
        for result in results:
            cursor.execute('''
                INSERT INTO results 
                (user_code, sound_code, emotion1, rating1, emotion2, rating2)
                VALUES (%s, %s, %s, %s, %s, %s)
            ''', (
                user_code,
                result.get('sound_code'),
                result.get('emotion1'),
                int(result.get('rating1')),
                result.get('emotion2') if result.get('emotion2') else None,
                int(result.get('rating2')) if result.get('rating2') else None
            ))

            cursor.execute('''
                UPDATE sound_samples 
                SET result_num = result_num + 1
                WHERE sound_code=  %s;
            ''', (result.get('sound_code'),))
        
        conn.commit()
        
        return jsonify({
            'success': True, 
            'message': 'Thank you for your submission!',
            'code': user_code
        })
    
    except psycopg2.Error as e:
        if conn:
            conn.rollback()
        return jsonify({'success': False, 'message': f'Database error: {str(e)}'}), 400
    except Exception as e:
        if conn:
            conn.rollback()
        return jsonify({'success': False, 'message': str(e)}), 400
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()

@app.route('/health')
def health_check():
    """Health check endpoint for monitoring"""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute('SELECT 1')
        cursor.close()
        conn.close()
        return jsonify({'status': 'healthy', 'database': 'connected'})
    except Exception as e:
        return jsonify({'status': 'unhealthy', 'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)