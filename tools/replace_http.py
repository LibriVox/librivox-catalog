#!/usr/bin/env python

import argparse
import pymysql
import re


MYSQL_USER = 'catalog'
MYSQL_PASS = 'changeme'
MYSQL_DB = 'librivox_catalog'


def replace_http(column, table, replace=False):
    HTTP_REGEX= re.compile(r'^http://')

    conn = pymysql.connect(user=MYSQL_USER, password=MYSQL_PASS,
                           database=MYSQL_DB,
                           unix_socket='/var/run/mysqld/mysqld.sock',
                           cursorclass=pymysql.cursors.DictCursor)

    with conn.cursor() as cursor:
        cursor.execute('SELECT id,%s FROM %s' % (column, table))
        rows = cursor.fetchall()
        for row in rows:
            url = row[column]
            if url and url.startswith('http://'):
                new_url = HTTP_REGEX.sub('https://', url)
                print('Will replace %s with %s... ' % (url, new_url),
                      end='', flush=True)
                update = ('UPDATE ' + table +
                          ' SET ' + column + '=%(url)s '
                          'WHERE id=%(id)s')
                if replace:
                    cursor.execute(update, {'id': row['id'], 'url': new_url})
                    print('Done.')
                else:
                    print('Dry run.')
    conn.commit()


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('-r', dest='replace', action='store_true',
                        default=False)
    args = parser.parse_args()

    for column in ['mp3_64_url', 'mp3_128_url' ]:
        replace_http(column, 'sections', replace=args.replace)
