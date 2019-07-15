#https://contenido.cont.ar/11819/20190706/stream.m3u8
#https://contenido.cont.ar/11820/20190710/stream.m3u8
#https://contenido.cont.ar/11821/20190710/stream.m3u8
#https://contenido.cont.ar/11822/20190710/stream.m3u8
#https://contenido.cont.ar/11824/20190710/stream.m3u8
#https://contenido.cont.ar/11825/20190710/stream.m3u8
#https://contenido.cont.ar/11826/20190710/stream.m3u8
#https://contenido.cont.ar/11827/20190710/stream.m3u8
#https://contenido.cont.ar/11828/20190711/stream.m3u8
#https://contenido.cont.ar/11829/20190711/stream.m3u8
#https://contenido.cont.ar/11830/20190711/stream.m3u8
#https://contenido.cont.ar/11831/20190711/stream.m3u8
#https://contenido.cont.ar/11832/20190711/stream.m3u8
#https://contenido.cont.ar/11833/20190712/stream.m3u8
#https://contenido.cont.ar/11834/20190712/stream.m3u8
#https://contenido.cont.ar/11834/20190712/stream.m3u8


echo finding el marginal
for i in $(seq 11834 11894) ; do 
    for z in {12..17} ; do
        
        DATI=$(printf "%02d" $z)
        URL="https://contenido.cont.ar/$i/201907$DATI/stream.m3u8"
        RES=$(curl -sI $URL | grep HTTP)

        if echo $RES | grep -q "200"; then
            echo $URL;
        fi
    done
done


