import os.path
import shutil
import hashlib
import logging
import urllib.request
import requests
from requests.auth import HTTPBasicAuth
import base64
import tarfile
import sys
import platform
import mmap
from pusher import Pusher


GFAST_DIRECTORY = '/home/pfi/sckipio'


pusher = Pusher(app_id=u'pfi', key=u'981847356', secret=u'asgaADFGA3451aSDFAGAGUIO456',
                ssl=False, host=u'pfi.test.com', port=4567)


def validate_file(file_path, hash):
    """
    Validates a file against an MD5 hash value
    :param file_path: path to the file for hash validation
    :type file_path:  string
    :param hash:      expected hash value of the file
    :type hash:       string -- MD5 hash value
    """
    m = hashlib.md5()
    with open(file_path, 'rb') as f:
        while True:
            chunk = f.read(1000 * 1000)  # 1MB
            if not chunk:
                break
            m.update(chunk)
    return m.hexdigest() == hash


def create_directory(dirName):
    try:
        # Create target Directory
        os.mkdir(dirName)
        print("Directory ", dirName,  " Created ")
    except FileExistsError:
        print("Directory ", dirName,  " already exists")


def create_file(file):
    try:
        with open(file, "w+") as f:
            f.close()
            # Do whatever with f
    except:
        # log exception
        print("Could not create ", file,  " as a file")


def untar(fname, folder_path):
    if (fname.endswith("tar.gz")):
        tar = tarfile.open(fname)
        tar.extractall(path=folder_path)
        tar.close()
        print("Extracted in Current Directory")
    else:
        print("Not a tar.gz file: '%s '" % sys.argv[0])


def try_download_headers(url):

    file_size = requests.head(url, stream=True, auth=HTTPBasicAuth(
        'pfi', 'pfi'))
    print(file_size.headers)


def get_file_content(file):
    with open(file, 'r') as theFile:
        # Return a list of lines (strings)
        # data = theFile.read().split('\n')

        # Return as string without line breaks
        # data = theFile.read().replace('\n', '')

        # Return as string
        data = theFile.read()
        return data


def check_for_driver(dir):
    if os.path.isfile(os.path.join(dir, 'gfast.ko')):
        print('G.Fast driver has been compiled')
        return True


def search_for_string(file, textString):
    byteString = textString.encode('ASCII')
    with open(file, 'rb', 0) as file, \
            mmap.mmap(file.fileno(), 0, access=mmap.ACCESS_READ) as s:

        if s.find(byteString) != -1:
            return True
    return False


def check_for_register():
    file = '/lib/modules/%s/build/net/8021q/vlan.c' % platform.release()
    if search_for_string(file, "EXPORT_SYMBOL_GPL(register_vlan_dev);") == False:
        print('Writing changes to vlan.c file')
        #handle = open(file, 'a')
        # handle.write("EXPORT_SYMBOL_GPL(register_vlan_dev);")
        # handle.close()
        os.system(
            "sudo echo '%s' >> %s" % ("EXPORT_SYMBOL_GPL(register_vlan_dev);", file))
    else:
        print('Changes already made to vlan.c')


def check_for_unregister():
    file = '/lib/modules/%s/build/net/8021q/vlan.c' % platform.release()
    if search_for_string(file, "EXPORT_SYMBOL_GPL(unregister_vlan_dev);") == False:
        print('Writing changes to vlan.c file')
        #handle = open(file, 'a')
        # handle.write("EXPORT_SYMBOL_GPL(register_vlan_dev);")
        # handle.close()
        os.system(
            "sudo echo '%s' >> %s" % ("EXPORT_SYMBOL_GPL(unregister_vlan_dev);", file))
    else:
        print('Changes already made to vlan.c')


def compile_new_8021q():
    kernel_dir = '/lib/modules/%s/build' % platform.release()
    a8021q_dir = os.path.join(kernel_dir, 'net', '8021q')
    os.system("sudo make -C %s M=%s modules" % (kernel_dir, a8021q_dir))
    pusher.trigger(u'gfast', u'install', {u'message': 'Compiled new driver'})
    print('Compiled 8021q ')


def install_new_8021q():
    kernel_dir = '/lib/modules/%s/build' % platform.release()
    a8021q_dir = os.path.join(kernel_dir, 'net', '8021q')
    os.system("sudo make -C %s M=%s modules_install" %
              (kernel_dir, a8021q_dir))
    os.system("sudo depmod")


def load_8021q():
    file = '/etc/modules'
    if search_for_string(file, "8021q") == False:
        os.system(
            "sudo echo '%s' >> %s" % ("8021q", file))


def load_gfast():
    file = '/etc/modules'
    if search_for_string(file, "gfast") == False:
        os.system(
            "sudo echo '%s' >> %s" % ("gfast", file))


def verify_and_modify_vlanc():
    check_for_register()
    check_for_unregister()
    compile_new_8021q()
    install_new_8021q()
    load_8021q()


def move_gfast_driver(dir):
    os.system('sudo mkdir -p /lib/modules/%s/kernel/net/gfast' %
              platform.release())
    os.system("sudo cp %s %s" % (os.path.join(dir, 'gfast.ko'),
                                 '/lib/modules/%s/kernel/net/gfast/gfast.ko' % platform.release()))
    os.system("sudo depmod")


def make_gfast_driver():
    # subprocess.Popen(["make"], stdout=subprocess.PIPE, cwd=os.path.join(
    #    GFAST_DIRECTORY, 'gfast-latest', 'gfast-driver'))
    build_dir = os.path.join(
        GFAST_DIRECTORY, 'gfast-latest', 'gfast-driver', 'gfast-driver', 'src')
    os.system("make -C %s all" % build_dir)
    if check_for_driver(build_dir) == True:
        move_gfast_driver(build_dir)
    load_gfast()


def download_with_resume(url, file_path, hash=None, timeout=10):
    """
    Performs a HTTP(S) download that can be restarted if prematurely terminated.
    The HTTP server must support byte ranges.
    :param file_path: the path to the file to write to disk
    :type file_path:  string
    :param hash: hash value for file validation
    :type hash:  string (MD5 hash value)
    """
    # don't download if the file exists

    file_name = os.path.basename(url)
    create_directory(file_path)
    full_name = os.path.join(file_path, file_name)

    if os.path.exists(full_name):
        return
    block_size = 1000 * 1000  # 1MB
    tmp_file_path = full_name + '.part'

    create_file(tmp_file_path)

    first_byte = os.path.getsize(tmp_file_path)
    logging.debug('Starting download at %.1fMB' % (first_byte / 1e6))
    file_size = -1
    try:
        file_size = int(requests.get(url, stream=True, auth=HTTPBasicAuth(
            'pfi', 'pfi')).headers['content-length'])
        logging.debug('File size is %s' % file_size)
        print('File size is %s' % file_size)
        while first_byte < file_size:
            last_byte = first_byte + block_size \
                if first_byte + block_size < file_size \
                else file_size
            logging.debug('Downloading byte range %d - %d' %
                          (first_byte, last_byte))

            headers = {"Range": 'bytes=%s-%s' %
                       (first_byte, last_byte)}  # first 100 bytes

            # create the request and set the byte range in the header
            response = requests.get(url, auth=HTTPBasicAuth(
                'pfi', 'pfi'), headers=headers, stream=True)

            for chunk in response.iter_content(chunk_size=block_size):
                if chunk:  # filter out keep-alive new chunks
                    with open(tmp_file_path, 'ab') as handle:
                        handle.write(chunk)
            first_byte = last_byte + 1
    except IOError as e:
        logging.debug('IO Error - %s' % e)
    finally:
        # rename the temp download file to the correct name if fully downloaded
        if file_size == os.path.getsize(tmp_file_path):
            # if there's a hash value, validate the file
            if hash and not validate_file(tmp_file_path, hash):
                raise Exception(
                    'Error validating the file against its MD5 hash')
            shutil.move(tmp_file_path, full_name)
            pusher.trigger(u'gfast', u'install', {
                           u'message': 'Successfully downloaded'})
        elif file_size == -1:
            raise Exception(
                'Error getting Content-Length from server: %s' % url)


def install_gfast_cli():
    cli_package = os.path.join(
        GFAST_DIRECTORY, 'gfast-latest', 'sckgfastcli-0.1_armhf.deb')
    python_package = os.path.join(
        GFAST_DIRECTORY, 'gfast-latest', 'python3-sckgfast-0.1_all.deb')

    if os.path.isfile(python_package):
        os.system("sudo dpkg -i %s " % python_package)

    if os.path.isfile(cli_package):
        os.system("sudo dpkg -i %s " % cli_package)
        os.system("sudo apt-get -f --yes --force-yes install")

    os.system("sudo modprobe gfast")
    pusher.trigger(u'gfast', u'install', {u'message': 'Install Complete'})


def main():

    # This script must be run as root!
    if not os.geteuid() == 0:
        sys.exit('This script must be run as root!')

    verify_and_modify_vlanc()
    download_with_resume(
        'https://test.com/media/sckipio/gfast-latest.checksum', GFAST_DIRECTORY)
    md5Hash = get_file_content(os.path.join(
        GFAST_DIRECTORY, 'gfast-latest.checksum'))
    download_with_resume(
        'https://test.com/media/sckipio/gfast-latest.tar.gz', GFAST_DIRECTORY)
    untar(os.path.join(GFAST_DIRECTORY, 'gfast-latest.tar.gz'),
          folder_path=GFAST_DIRECTORY)

    if os.path.exists(os.path.join(GFAST_DIRECTORY, 'gfast-latest', 'gfast-driver.tar.gz')):
        untar(os.path.join(GFAST_DIRECTORY,
                           'gfast-latest', 'gfast-driver.tar.gz'), os.path.join(GFAST_DIRECTORY,
                                                                                'gfast-latest'))
    make_gfast_driver()

    install_gfast_cli()


if __name__ == '__main__':
    main()
