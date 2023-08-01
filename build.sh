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
