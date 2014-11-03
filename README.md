# Nagios Apdex Generator

Create Apdex Scoring based on Nagios Log File

This set of scripts does 2 things:

1. Creates a database and table in MySQL to allow you store data from a Nagios log file
2. Imports the data from a Nagios log file into that database

# Install

Edit the install.sh to include your MySQL server admin details (the script is set up for MySQL server running on localhost)
Run:

`./install`

(Note: this will overwrite any existing installation)

The default values used by the script are:

Database name: nagios
Database user: nagiosuser
Database password: nagiosuser

# Run import

`./import [-v] <nagios.log>`

The -v argument will output the MySQL commands as they are running.
