# query.py
# Updated module to query book data with enhanced error handling and flexible query

import pymysql
import json

class BookQuery:
    def __init__(self, host='localhost', user='root', password='', db='nckh'):
        try:
            self.conn = pymysql.connect(host=host, user=user, password=password, db=db, charset='utf8mb4')
        except Exception as e:
            print(f"DEBUG: Could not connect to database: {e}")
            self.conn = None

    def find_books(self, genre=None, mood=None, level=None):
        if not self.conn:
            print("DEBUG: No database connection available for query.")
            return []
        try:
            with self.conn.cursor(pymysql.cursors.DictCursor) as cursor:
                sql = "SELECT tensanpham, author, mota, dongia, category FROM products WHERE 1=1"
                params = []

                if genre:
                    cat_id = self.get_category_id(genre)
                    print(f"DEBUG: Mapping genre '{genre}' to category ID '{cat_id}'.")
                    if cat_id is not None:
                        sql += " AND category = %s"
                        params.append(cat_id)
                    else:
                        print(f"DEBUG: Genre '{genre}' has no mapping to category ID.")
                else:
                    print("DEBUG: No genre specified, querying top priced books.")

                # Extended with mood or level filters if needed in future

                sql += " ORDER BY dongia DESC LIMIT 5"

                print(f"DEBUG: Executing SQL: {sql} with params {params}")

                cursor.execute(sql, params)
                results = cursor.fetchall()
                print(f"DEBUG: Query returned {len(results)} books.")
                return results
        except Exception as e:
            print(f"DEBUG: Database query error: {e}")
            return []

    def get_category_id(self, genre_name):
        # Update this mapping based on your actual database categories
        genre_to_id = {
            'IT': 1,
            'Finance': 2,
            'Life skills': 3,
            'Psychology': 4,
            'Romance': 5,
            'Science Fiction': 6,
        }
        return genre_to_id.get(genre_name, None)
