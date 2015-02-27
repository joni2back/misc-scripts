json() {
   echo "$1" | python -mjson.tool
}

LOOP=50
loop() {
    for i in $(seq 1 $LOOP) ; do
        $@
    done
}

diff() {
    svn diff $@ | vim -c "colorscheme zellner" -
}

function prompt {
    green=$(tput setaf 2)
    blue=$(tput setaf 4)
    bold=$(tput bold)
    reset=$(tput sgr0)
    PS1='\[$green$bold\]\u\[$reset\]:\[$blue$bold\]\w\[$reset\]\$ '
}

function readfrompipe {
    (while read "i" ;  do echo \| $i ;done)
}

prompt
http_proxy=""
