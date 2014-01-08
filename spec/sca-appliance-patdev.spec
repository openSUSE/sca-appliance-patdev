# Copyright (C) 2013 SUSE LLC
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.
#
# norootforbuild
# neededforbuild
%define sca_common sca

Name:         sca-appliance-patdev
Summary:      Supportconfig Analysis Appliance Pattern Development
URL:          https://bitbucket.org/g23guy/sca-appliance-patdev
Group:        Documentation/SuSE
Distribution: SUSE Linux Enterprise
Vendor:       SUSE Support
License:      GPL-2
Autoreqprov:  on
Version:      1.2
Release:      1.140108.PTF.2
Source:       %{name}-%{version}.tar.gz
BuildRoot:    %{_tmppath}/%{name}-%{version}
Buildarch:    noarch
Requires:     apache2
Requires:     /usr/sbin/mysqld
Requires:     sca-appliance-common
Requires:     sca-patterns-base

%description
The SCA Appliance allows for adding custom patterns. This package
provides a database used to create pattern templates, speeding up
custom pattern development.

Authors:
--------
    Jason Record <jrecord@suse.com>

%prep
%setup -q

%build
gzip -9f man/*

%install
pwd;ls -la
rm -rf $RPM_BUILD_ROOT
install -d $RPM_BUILD_ROOT/etc/opt/%{sca_common}
install -d $RPM_BUILD_ROOT/opt/%{sca_common}/bin
install -d $RPM_BUILD_ROOT/srv/www/htdocs/sdp
install -d $RPM_BUILD_ROOT/usr/sbin
install -d $RPM_BUILD_ROOT/usr/bin
install -d $RPM_BUILD_ROOT/usr/share/man/man1
install -d $RPM_BUILD_ROOT/usr/share/man/man5
install -d $RPM_BUILD_ROOT/usr/share/doc/packages/%{sca_common}
install -d $RPM_BUILD_ROOT/var/opt/%{sca_common}
install -d $RPM_BUILD_ROOT/var/archives
install -m 644 config/*.conf $RPM_BUILD_ROOT/etc/opt/%{sca_common}
install -m 644 config/* $RPM_BUILD_ROOT/usr/share/doc/packages/%{sca_common}
install -m 544 bin/* $RPM_BUILD_ROOT/opt/%{sca_common}/bin
install -m 555 bin/pat $RPM_BUILD_ROOT/usr/bin
install -m 544 bin/sdpdb $RPM_BUILD_ROOT/usr/sbin
install -m 544 bin/setup-sdp $RPM_BUILD_ROOT/usr/sbin
install -m 644 websdp/* $RPM_BUILD_ROOT/srv/www/htdocs/sdp
install -m 400 websdp/db-config.php $RPM_BUILD_ROOT/srv/www/htdocs/sdp
install -m 644 schema/* $RPM_BUILD_ROOT/usr/share/doc/packages/%{sca_common}
install -m 644 docs/README* $RPM_BUILD_ROOT/usr/share/doc/packages/%{sca_common}
install -m 644 man/*.1.gz $RPM_BUILD_ROOT/usr/share/man/man1
install -m 644 man/*.5.gz $RPM_BUILD_ROOT/usr/share/man/man5

%files
%defattr(-,root,root)
%dir /opt
%dir /etc/opt
%dir /var/opt
%dir %attr(1777,root,root) /var/archives
%dir /srv/www/htdocs/sdp
%dir /opt/%{sca_common}
%dir /opt/%{sca_common}/bin
%dir /etc/opt/%{sca_common}
%dir /var/opt/%{sca_common}
%dir /usr/share/doc/packages/%{sca_common}
/usr/sbin/*
/usr/bin/*
/opt/%{sca_common}/bin/*
%config /etc/opt/%{sca_common}/*
%doc /usr/share/man/man1/*
%doc /usr/share/man/man5/*
%attr(-,wwwrun,www) /srv/www/htdocs/sdp
%doc /usr/share/doc/packages/%{sca_common}/*

%changelog
* Wed Jan 08 2014 jrecord@suse.com
- sdpdb man page has correct name
- binaries installed in correct locations
- fixed hash plings in template php pages

* Fri Jan 03 2014 jrecord@suse.com
- added pat documentation
- added pat pattern tester
- separated sca-appliance-common files

* Thu Dec 20 2013 jrecord@suse.com
- separated as individual RPM package

