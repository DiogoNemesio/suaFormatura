#!/usr/bin/perl

@sqls = `find . -name *.sql`;

for $sql (@sqls) {
        system("mysql DBApp -f < $sql");
}
