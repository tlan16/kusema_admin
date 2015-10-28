#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

php $DIR/pullPerson.php
php $DIR/pullTopic.php
php $DIR/pullUnit.php
php $DIR/pullQuestion.php
php $DIR/pullAnswer.php
php $DIR/pullComments.php