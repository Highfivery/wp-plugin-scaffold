#!/bin/sh
slugify () {
    echo "$1" | iconv -c -t ascii//TRANSLIT | sed -E 's/[~^]+//g' | sed -E 's/[^a-zA-Z0-9]+/-/g' | sed -E 's/^-+|-+$//g' | tr A-Z a-z
}

read -p "What is the plugin's name? " plugin_name

read -p "What is the plugin's shortname? " plugin_short_name

read -p "What is the plugin's description? " plugin_description

read -p "What is the plugin's URI? " plugin_uri

read -p "What is the name of the plugin's author [Highfivery LLC]? " plugin_author
plugin_author=${plugin_author:-Highfivery LLC}

read -p "What is the name of the plugin's author URI [https://www.highfivery.com]? " plugin_author_uri
plugin_author_uri=${plugin_author_uri:-https://www.highfivery.com}

read -p "What license should the plugin use [GPL v2 or later]? " plugin_license
plugin_license=${plugin_license:-GPL v2 or later}

read -p "What's the license URI [https://www.gnu.org/licenses/gpl-2.0.html]? " plugin_license_uri
plugin_license_uri=${plugin_license_uri:-https://www.gnu.org/licenses/gpl-2.0.html}

plugin_slug=$(slugify "$plugin_name")
read -p "What is the plugin's text domain [$plugin_slug]? " text_domain
text_domain=${text_domain:-${plugin_slug}}

read -p "What is the name of the plugin's function prefix? " function_prefix

read -p "What is the plugin's contant name? " plugin_constant

read -p "What is the plugin's package name? " package_name

echo
# Header
echo -e "\033[1mPlugin Information:\033[0m"

# Body
printf "%-20s %s\n" "Name:" "$plugin_name"
printf "%-20s %s\n" "Shortname:" "$plugin_short_name"
printf "%-20s %s\n" "Description:" "$plugin_description"
printf "%-20s %s\n" "URI:" "$plugin_uri"
printf "%-20s %s\n" "Author:" "$plugin_author ($plugin_author_uri)"
printf "%-20s %s\n" "Plugin License:" "$plugin_license ($plugin_license_uri)"
printf "%-20s %s\n" "Text Domain:" "$text_domain"
printf "%-20s %s\n" "Function Prefix:" "$function_prefix"
printf "%-20s %s\n" "Constant:" "$plugin_constant"
printf "%-20s %s\n" "Package Name:" "$package_name"

# Footer
echo

read -r -p "Does this all look right? [y/N] " response
case "$response" in
  [yY][eE][sS]|[yY])
    mkdir -p $text_domain
    rsync -av --exclude='.git' --exclude='.husky' --exclude='docs' --exclude='node_modules' --exclude=$text_domain --exclude='LICENSE' --exclude='README.md' --exclude='create.sh' . $text_domain
    cd $text_domain
    mv wp-plugin-scaffold.php "$text_domain.php"
    find ./ -type f -exec sed -i '' -e "s~WordPress Plugin Scaffold~$plugin_name~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~WPPluginScaffold~$package_name~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~A plugin WordPress scaffold to help you get started quickly.~$plugin_description~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~wp-plugin-scaffold~$text_domain~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~https://highfivery.com~$plugin_author_uri~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~https://project-website.tld~$plugin_uri~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~https://github.com/Highfivery/wp-plugin-scaffold~$plugin_uri~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~Highfivery~$plugin_author~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~WP_PLUGIN_SCAFFOLD~$plugin_constant~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~wp_plugin_scaffold~$function_prefix~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~https://www.gnu.org/licenses/gpl-2.0.html~$plugin_license_uri~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~GPL v2 or later~$plugin_license~g" {} \;
    find ./ -type f -exec sed -i '' -e "s~WP Plugin Scaffold~$plugin_short_name~g" {} \;
    cd ..
    mv $text_domain ../
    ;;
  *)
    false
    ;;
esac
