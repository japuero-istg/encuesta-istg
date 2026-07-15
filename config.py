import os
from dotenv import load_dotenv

load_dotenv()

DATABASE_URL = os.getenv("DATABASE_URL", "postgresql://admin:admin123@localhost:5432/encuesta_istg")
ADMIN_USER = os.getenv("ADMIN_USER", "admin")
ADMIN_PASS = os.getenv("ADMIN_PASS", "istg2026")
SECRET_KEY = os.getenv("SECRET_KEY", "clave-secreta-cambiar-en-produccion")
