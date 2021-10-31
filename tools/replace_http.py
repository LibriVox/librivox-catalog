#!/usr/bin/env python

import argparse
import pymysql
import re


MYSQL_USER = 'catalog'
MYSQL_PASS = 'changeme'
MYSQL_DB = 'librivox_catalog'


def replace_http(column, replace=False):
    HTTP_REGEX= re.compile(r'^http://')

    conn = pymysql.connect(user=MYSQL_USER, password=MYSQL_PASS,
                           database=MYSQL_DB,
                           unix_socket='/var/run/mysqld/mysqld.sock',
                           cursorclass=pymysql.cursors.DictCursor)

    with conn.cursor() as cursor:
        cursor.execute('SELECT id,%s FROM projects' % column)
        # NOTE(artom) We could be smarter about this, but we only have ~16500
        # projects.
        projects = cursor.fetchall()
        for project in projects:
            url = project[column]
            if url.startswith('http://'):
                new_url = HTTP_REGEX.sub('https://', url)
                print('Will replace %s with %s... ' % (url, new_url),
                      end='', flush=True)
                update = ('UPDATE projects '
                          'SET ' + column + '=%(url)s '
                          'WHERE id=%(id)s')
                if replace:
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

    for column in [
        'url_librivox', 'url_forum', 'coverart_pdf', 'coverart_jpg',
        'coverart_thumbnail', 'url_text_source', 'url_project'
    ]:
        replace_http(column, replace=args.replace)
