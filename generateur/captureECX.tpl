#!/bin/bash

# Please change this to the right HTTP user:pass to be used with encyclix
BASEURL=http://user:password@isidore/encyclix/
xvfb-run --server-args="-screen 0, 1024x768x24" cutycapt --url=${BASEURL}rendu/parser.php?name=$1 --out=$1-g.png --out-format=png
#$1-g est l'image non croppée. "convert" rend deux images : le crop et le résidu. on garde que le crop.

convert -crop '652x100000>' $1-g.png $1.png
rm $1-1.png
rm $1-g.png

mv $1-0.png $1.png

#Puis déplacement dans le dossier de l'ecx en question
mv $1.png ../encyclixes/$1/$1.png
