import sqlite3

class MarketplaceManager:
    def __init__(self, db_path="ivasmss.db"):
        self.db_path = db_path
        self._init_db()

    def _init_db(self):
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT UNIQUE,
                price REAL,
                status TEXT
            )
        ''')
        conn.commit()
        conn.close()

    def get_services(self):
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        cursor.execute("SELECT id, name, price, status FROM services")
        rows = cursor.fetchall()
        conn.close()
        return [{"id": r[0], "name": r[1], "price": r[2], "status": r[3]} for r in rows]

    def add_service(self, name, price):
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        cursor.execute("INSERT OR REPLACE INTO services (name, price, status) VALUES (?, ?, ?)", (name, price, "active"))
        conn.commit()
        conn.close()
