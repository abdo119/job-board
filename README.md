I Use MySql Server As Database <br>
I used Seeders with Factory to Create 20 Job , attributes , categories
,locations and languages <br>
If you clone project from repo please make sure to execute this instructions sequentially

##### 1- Firstly run  ==> composer update <==

##### 2- Secondly run  ==> php artisan migrate <==

##### 3- Thirdly run ==> **php artisan migrate:fresh --seed** <== to generate data <br>

##### 4- Finally don't forget run ==> alias devup='php artisan cache:clear && php artisan route:clear && php artisan config:clear && php artisan view:clear'

#####   then run ==>  devup  <==  to clear cache and route <br>

Link of Repository on gitHub  =====>    https://github.com/abdo119/job-board


