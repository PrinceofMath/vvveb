#!/bin/bash

#find app admin install system config storage/compiled-templates -name '*.*' > POTFILES
find . -not -path "*/node_modules/*" -type f \( -iname \*.php -o -iname \*.html \)  > POTFILES
xgettext --lang=ro_RO  -LPHP --files-from=POTFILES -o locale/ro_RO/LC_MESSAGES/vvveb.pot
msgmerge -U  --lang=ro_RO locale/ro_RO/LC_MESSAGES/vvveb.pot locale/ro_RO/LC_MESSAGES/vvveb.po
#rm POTFILES
