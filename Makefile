APP_LOC=${HOME}/homescape
SITE_LOC=${HOME}/public_html/homescape

all: base
	# mysql refresh
	mysql -Dhomescape_test -uhomescape_test -phomescape_test < sql/homescape_drop.sql
	mysql -Dhomescape_test -uhomescape_test -phomescape_test < sql/homescape_create.sql
	mysql -Dhomescape_test -uhomescape_test -phomescape_test < sql/contact_initial_load.sql
	# hack to swap the user to the current one
	echo "swapping user to ${LOGNAME}"
	echo
	./swap_user ${SITE_LOC}
	echo

base:
	echo "rebuilding ${SITE_LOC}"
	echo
	rm -rf ${SITE_LOC}
	cp -R site ${SITE_LOC}
	find ${SITE_LOC} -name CVS | xargs rm -rf
	find ${SITE_LOC} -type d | xargs chmod 770
	find ${SITE_LOC} -type f | xargs chmod 660
	chgrp -R apache ${SITE_LOC}
	echo
	echo "rebuilding ${APP_LOC}"
	echo
	rm -rf ${APP_LOC}
	mkdir ${APP_LOC}
	mkdir ${APP_LOC}/config
	mkdir ${APP_LOC}/incoming
	mkdir ${APP_LOC}/media
	mkdir ${APP_LOC}/smarty
	mv ${SITE_LOC}/smarty ${APP_LOC}/smarty/smarty
	mv ${SITE_LOC}/templates ${APP_LOC}/smarty
	mkdir ${APP_LOC}/smarty/templates_c
	mkdir ${APP_LOC}/smarty/cache
	mkdir ${APP_LOC}/smarty/configs
	mv ${SITE_LOC}/admin/.ht_users ${APP_LOC}/config
	mv ${SITE_LOC}/admin/.ht_groups ${APP_LOC}/config
	#ln -sf ${HOME}/Nate_Rental_1 ${APP_LOC}/incoming/test1
	cp -R ${HOME}/Nate_Rental_1/* ${APP_LOC}/incoming
	find ${APP_LOC} -type d | xargs chmod 770
	find ${APP_LOC} -type f | xargs chmod 660
	chgrp -R apache ${APP_LOC}
	chmod 2770 ${APP_LOC}/media
	echo
