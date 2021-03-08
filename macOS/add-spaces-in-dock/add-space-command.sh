num=1
limit=$1

# Add number of spaces you want
while [ $num -le $limit ]
do
	defaults write com.apple.dock persistent-apps -array-add '{"tile-type"="spacer-tile";}';
	((num=num + 1))
done

# Reset num var
((num=1))

# Reload Dock
killall Dock