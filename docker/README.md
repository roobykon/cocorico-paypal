1. docker-compose up -d
2. docker exec [web_container_name] post-install.sh install
3. edit in ./conf/parameters.yml next parameters
database_host: 
database_port: 
database_name: 
database_user: 
database_password: 
mongodb_server: 
mongodb_database_name: 
cocorico.assets_base_urls: 
router.request_context.host: 
cocorico.facebook.app_id: 
cocorico.facebook.secret: 
cocorico.google_analytics: 
cocorico_geo.google_place_api_key: 
cocorico_geo.google_place_server_api_key: 
cocorico.paypal.account1.application_id: APP-80W284485P519543T
cocorico.paypal.account1.username: 'Account username'
cocorico.paypal.account1.password: 'Account password'
cocorico.paypal.account1.signature: 'Account signature' 
cocorico.paypal.mode: sandbox
4. cp ./conf/parameters.yml ./data/web/data/app/config/parameters.yml
5. docker exec [web_container_name] post-install.sh installdeps
6. docker exec [web_container_name] post-install.sh config