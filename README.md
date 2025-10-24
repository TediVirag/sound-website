# Sound Testing Web Application

A simple web application with a questionnaire form that stores responses in a PostgreSQl database.

# Local set up
# 1. Install PostgreSQL (see INSTALL.md for your OS)

# 2. Create project directory
mkdir questionnaire-app && cd questionnaire-app

# 3. Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# 4. Install dependencies
pip install -r requirements.txt

# 5. Set up environment variables
cp .env.example .env
# Edit .env with your PostgreSQL password

# 6. Initialize database
python database.py

# 7. Run the application
python app.py

# 8. Open browser to http://localhost:5000

