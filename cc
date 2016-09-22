#!/bin/bash
# Symfony2 clear cache, should be chmod 755  before run
echo "Lazy cache clear started"
app/console cache:clear
app/console as:in --symlink
app/console as:du --env=prod
chmod 777 -R app/cache
chmod 777 -R app/logs
app/console f:e:p
exit
