echo "reset log"
rm ./src/log.txt
touch ./src/log.txt

rm -rf ./dist

echo "initializing dist folder"
mkdir -p ./dist/assets/
mkdir -p ./dist/branches/
mkdir -p ./dist/tags/
mkdir -p ./dist/trunk/

echo "adding assets files"
cp -r ./assets ./dist/

echo "syncing trunk folder"
rsync -r --delete --no-links ./src/* ./dist/trunk/
rsync -r --delete --no-links ./src/* /Users/yan/PHP/svn/beans-woocommerce-trunk/

# cd /Users/yan/PHP/svn/beans-woocommerce-trunk/
# # Fetch remote repo:
# svn update
# # Check changes
# svn status
# svn add|delete on each added/deleted file 
# svn commit -m "Beans WooCommerce 3.6"
# help: https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/