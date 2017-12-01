#cd /Applications/MAMP/htdocs/Dashboard
#ls
#git status
#git add .
## git remove BillboardConfig.ini
#git status
#git commit -F, --file CommitMessage.txt
#git push
#read -p "Do you want to deploy this version? (Y/N)" -n 1 -r
#echo
## echo "You said $REPLY."
#if [[ $REPLY =~ ^[Yy]$ ]]
#then
#echo "Merging current version with master branch..."
#git checkout master
#git merge v2.0.0
#git push
#git checkout v2.0.0
#fi
#echo "Finished!"