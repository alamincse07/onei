# onei
onei 


# Create folder at the project root level like:
data

  ├── details-page-contents

  ├── page-contents

  ├──properties

 # build docker image 
 - Go to script folder
 `docker build -t onei .`
 - Then start the process of scrapping
 - `docker run -t --name onei -v $(pwd):/code onei `
- ` bash execute_bash_commands.sh`