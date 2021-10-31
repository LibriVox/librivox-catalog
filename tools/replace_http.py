#!/usr/bin/env python

import argparse
import pymysql
import re


MYSQL_USER = 'catalog'
MYSQL_PASS = 'changme'
MYSQL_DB = 'librivox_catalog'


def replace_http(replace=False):
    HTTP_REGEX= re.compile(r'^http://')

    conn = pymysql.connect(user=MYSQL_USER, password=MYSQL_PASS,
                           database=MYSQL_DB,
                           unix_socket='/var/run/mysqld/mysqld.sock',
                           cursorclass=pymysql.cursors.DictCursor)

    with conn.cursor() as cursor:
        cursor.execute('SELECT id,zip_url FROM projects')
        # NOTE(artom) We could be smarter about this, but we only have ~16500
        # projects.
        projects = cursor.fetchall()
        for project in projects:
            url = project['zip_url'].strip('\"\'')
            if url.startswith('http://'):
                new_url = HTTP_REGEX.sub('https://', url)
                print('Will replace %s with %s... ' % (url, new_url),
                      end='', flush=True)
                if replace:
                    update = ('UPDATE projects '
                              'SET zip_url=%(url)s '
                              'WHERE id=%(id)s')
                    cursor.execute(update, {'id': project['id'], 'url': new_url})
                    print('Done.')
                else:
                    print('Dry run.')
    conn.commit()


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('-r', dest='replace', action='store_true',
                        default=False)
    args = parser.parse_args()

    replace_http(replace=args.replace)
