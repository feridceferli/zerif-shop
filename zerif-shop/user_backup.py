import shutil
import datetime
import os

def backup_database(db_path="ivasmss.db", backup_dir="backups"):
    if not os.path.exists(db_path):
        print(f"Database {db_path} does not exist.")
        return None
    
    os.makedirs(backup_dir, exist_ok=True)
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    backup_path = os.path.join(backup_dir, f"ivasmss_backup_{timestamp}.db")
    
    shutil.copy(db_path, backup_path)
    print(f"Backup created successfully at {backup_path}")
    return backup_path

if __name__ == "__main__":
    backup_database()
