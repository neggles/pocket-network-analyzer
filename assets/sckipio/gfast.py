from sckgfast import GfastCli


with GfastCli() as cli:
	#cli.get_device_info()
	response=cli.get_gfast_counters()
	print(response)