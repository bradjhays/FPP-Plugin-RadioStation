#!/usr/bin/env python
"""."""
import sys
import configparser
import pprint
import glob
import json

from mutagen.mp3 import MP3

config = configparser.ConfigParser()


TEMPLATE = {
    "name": "tester",
    "version": 3,
    "repeat": 1,
    "loopCount": 0,
    "empty": False,
    "desc": "testing123",
    "random": 2,
    "leadIn": [],
    "mainPlaylist": [],
    "leadOut": [],
    "playlistInfo": {"total_duration": 0, "total_items": 0},
}


def add_file_to_playlist(filename, remove_prefix=""):
    """."""
    fname = filename.split("media/music/")[1]
    audio = MP3(filename)
    # print(audio.info.length)
    return {
        "type": "media",
        "enabled": 1,
        "playOnce": 0,
        "fileMode": "single",
        "mediaName": f"{fname}",
        "videoOut": "--Default--",
        "note": fname.split(".")[0].replace(remove_prefix, ""),
        "duration": audio.info.length,
    }


def get_music_files_starting_with(path):
    """."""
    globber = f"{path}*.[mf][pl][3a]*"
    print(f"glob: {globber}")
    return glob.glob(globber)


def create_playlist(name, config_info, remove_prefix=""):
    """ "."""
    ret = dict(TEMPLATE)
    ret["name"] = name
    ret["desc"] = name
    for music in config_info["files"]:
        ret["mainPlaylist"].append(
            add_file_to_playlist(music, remove_prefix=remove_prefix)
        )
    total_items = 0
    total_duration = 0
    for item in ret["mainPlaylist"]:
        total_duration += float(item["duration"])
        total_items += 1
    ret["playlistInfo"] = {"total_duration": total_duration, "total_items": total_items}
    return ret


def save_playlist_config(media_dir, name, pl_config):
    """."""
    playlist_file = f"{media_dir}playlists/{name}.json"
    print(f"save to {playlist_file}")
    with open(playlist_file, "w", encoding="utf-8") as fobj:
        fobj.write(json.dumps(pl_config, indent=4))
    print("saved")


def main():
    """."""
    # print("main")
    # print("Argument List:", str(sys.argv))
    assert sys.argv[1], "need path to plugin"
    media_dir = sys.argv[1].split("plugins/")[0]
    # /home/fpp/media/plugins/FPP-Plugin-RadioStation
    # /home/fpp/media/config/plugin.RadioStation
    # print(media_dir)
    config.read(f"{media_dir}config/plugin.RadioStation")
    my_config_parser_dict = {s: dict(config.items(s)) for s in config.sections()}
    # print(f"config: {pprint.pformat(my_config_parser_dict)}")

    playlists = {}
    for section, info in my_config_parser_dict.items():
        if section.startswith("PL"):
            # print(info)
            name = info["playlist_name"].strip('"')
            playlists[name] = {}
            prefix = info["prefix"].strip('"')
            playlists[name]["path"] = f"{media_dir}music/{prefix}"
            playlists[name]["prefix"] = prefix
            # print(f"{name}=> {prefix}")
            playlists[name]["files"] = get_music_files_starting_with(
                path=playlists[name]["path"]
            )
            playlist_config = create_playlist(
                name=name,
                config_info=playlists[name],
                remove_prefix=playlists[name]["prefix"],
            )
            print(f"{name}\n{pprint.pformat(playlist_config)}")
            save_playlist_config(
                media_dir=media_dir, name=name, pl_config=playlist_config
            )
    print(pprint.pformat(playlists))


if __name__ == "__main__":
    main()
