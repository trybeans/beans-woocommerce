rm -rf ./dist

echo "initializing dist folder"
mkdir -p ./dist/assets/
mkdir -p ./dist/branches/
mkdir -p ./dist/tags/
mkdir -p ./dist/trunk/

echo "adding assets files"
cp -r ./assets ./dist/assets/

echo "syncing trunk folder"
rsync --no-links ./src/ ./dist/trunk/
