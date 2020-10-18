#!/bin/sh
#
# Docker "New Relic install process.
# Author: Kevin Wenger
#
# Run as `./newrelic.sh`
#
if [ -z "$NEW_RELIC_AGENT_VERSION" ]
then
  echo "NewRelic agent installation skipped"
else
  echo "NewRelic agent installation started"

  # Download and install NewRelic PHP extension. This generates newrelic.ini file at /usr/local/etc/php/conf.d/newrelic.ini
  curl -L "https://download.newrelic.com/php_agent/archive/${NEW_RELIC_AGENT_VERSION}/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-linux.tar.gz" | tar -C /tmp -zx \
   && export NR_INSTALL_USE_CP_NOT_LN=1 \
   && export NR_INSTALL_SILENT=1 \
   && /tmp/newrelic-php5-*/newrelic-install install \
   && rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

  # Set parameter values in newrelic.ini file e.g. license key
  sed -i -e s/\"REPLACE_WITH_REAL_KEY\"/${NEW_RELIC_LICENSE_KEY}/ \
   -e s/newrelic.appname[[:space:]]=[[:space:]].\*/newrelic.appname="${NEW_RELIC_APPNAME}"/ \
   -e s/\;newrelic.daemon.address[[:space:]]=[[:space:]].\*/newrelic.daemon.address="${NEW_RELIC_DAEMON_ADDRESS}"/ \
      /usr/local/etc/php/conf.d/newrelic.ini
fi
