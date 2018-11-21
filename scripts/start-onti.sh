#!/usr/bin/expect -f

set timeout 30

log_user 0

spawn screen -S onti

expect "Press Space for next page; Return to end"

send "\r"

expect "#"

send "cd /home/go/src/github.com/ontio/ontology\r"

expect "#"

send "./ontology --testmode\r"

expect "Password"

send "123456\r"

expect "INFO"

send "\x01"; send "d"

