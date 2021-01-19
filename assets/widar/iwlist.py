#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright 2012-2016 Brandon Bailey
# All Rights Reserved.

import re
import time
import sys
import subprocess
import signal
import threading

from pusher import Pusher

pusher = Pusher(app_id=u'pfi', key=u'981847356', secret=u'asgaADFGA3451aSDFAGAGUIO456',
                ssl=False, host=u'pfi.test.com', port=4567)

cellNumberRe = re.compile(
    r"^Cell\s+(?P<cellnumber>.+)\s+-\s+Address:\s(?P<mac>.+)$")
regexps = [
    re.compile(r"^ESSID:\"(?P<essid>.*)\"$"),
    re.compile(r"^Protocol:(?P<protocol>.+)$"),
    re.compile(r"^Mode:(?P<mode>.+)$"),
    re.compile(
        r"^Frequency:(?P<frequency>[\d.]+) (?P<frequency_units>.+) \(Channel (?P<channel>\d+)\)$"),
    re.compile(r"^Encryption key:(?P<encryption>.+)$"),
    re.compile(
        r"^Quality=(?P<signal_quality>\d+)/(?P<signal_total>\d+)\s+Signal level=(?P<signal_level_dBm>.+) d.+$"),
    re.compile(r"^Signal level=(?P<signal_quality>\d+)/(?P<signal_total>\d+).*$"),
]


try:
    import builtins
except ImportError:
    def print_(*args, **kwargs):
        """The new-style print function for Python 2.4 and 2.5.
        Taken from https://pypi.python.org/pypi/six/
        Modified to set encoding to UTF-8 if not set when stdout may not be
        a tty such as when piping to head
        """
        fp = kwargs.pop("file", sys.stdout)
        if fp is None:
            return

        def write(data):
            if not isinstance(data, basestring):
                data = str(data)
            # If the file has an encoding, encode unicode with it.
            encoding = fp.encoding or 'UTF-8'  # Diverges for notty
            if (isinstance(fp, file) and
                    isinstance(data, unicode) and
                    encoding is not None):
                errors = getattr(fp, "errors", None)
                if errors is None:
                    errors = "strict"
                data = data.encode(encoding, errors)
            fp.write(data)
        want_unicode = False
        sep = kwargs.pop("sep", None)
        if sep is not None:
            if isinstance(sep, unicode):
                want_unicode = True
            elif not isinstance(sep, str):
                raise TypeError("sep must be None or a string")
        end = kwargs.pop("end", None)
        if end is not None:
            if isinstance(end, unicode):
                want_unicode = True
            elif not isinstance(end, str):
                raise TypeError("end must be None or a string")
        if kwargs:
            raise TypeError("invalid keyword arguments to print()")
        if not want_unicode:
            for arg in args:
                if isinstance(arg, unicode):
                    want_unicode = True
                    break
        if want_unicode:
            newline = unicode("\n")
            space = unicode(" ")
        else:
            newline = "\n"
            space = " "
        if sep is None:
            sep = space
        if end is None:
            end = newline
        for i, arg in enumerate(args):
            if i:
                write(sep)
            write(arg)
        write(end)
else:
    print_ = getattr(builtins, 'print')
    del builtins


class ScanException(Exception):
    """Base exception for this module"""

# Runs the comnmand to scan the list of networks.
# Must run as super user.
# Does not specify a particular device, so will scan all network devices.


def scan(interface='wlan0'):

    global SHUTDOWN_EVENT, SOURCE, SCHEME, DEBUG
    SHUTDOWN_EVENT = threading.Event()

    signal.signal(signal.SIGINT, ctrl_c)

    signal.signal(signal.SIGTERM, ctrl_c)

    cmd = ["iwlist", interface, "scan"]
    while True:
        proc = subprocess.Popen(
            cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        points = proc.stdout.read().decode('utf-8')
        # print parse(points)
        cells = parse(points)
        print(cells)
        pusher.trigger(u'widar', u'scan', {u'message': cells})
    time.sleep(1)


def get_exception():
    """Helper function to work with py2.4-py3 for getting the current
    exception in a try/except block
    """
    return sys.exc_info()[1]

# Parses the response from the command "iwlist scan"


def parse(content):
    cells = []
    lines = content.split('\n')
    for line in lines:
        line = line.strip()
        cellNumber = cellNumberRe.search(line)
        if cellNumber is not None:
            cells.append(cellNumber.groupdict())
            continue
        for expression in regexps:
            result = expression.search(line)
            if result is not None:
                cells[-1].update(result.groupdict())
                continue
    return cells


def ctrl_c(signum, frame):
    """Catch Ctrl-C key sequence and set a SHUTDOWN_EVENT for our threaded
    operations
    """

    SHUTDOWN_EVENT.set()
    print_('\nCancelling...')
    sys.exit(0)


def main():
    try:
        scan()
    except KeyboardInterrupt:
        print_('\nCancelling...')
    except (ScanException, SystemExit):
        e = get_exception()
        if getattr(e, 'code', 1) != 0:
            raise SystemExit('ERROR: %s' % e)


if __name__ == '__main__':
    main()
