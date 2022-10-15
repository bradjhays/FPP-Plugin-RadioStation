#!/bin/bash -ex
exec 2>&1
cd "$(dirname "$0")"

# sudo apt install python3 python3-pip
pip install -r requirements.txt
python3 -m generate_playlist $@