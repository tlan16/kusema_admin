#!/bin/bash
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

php $DIR/pullUser.php 
php $DIR/pullUnit.php
php $DIR/pullTopic.php