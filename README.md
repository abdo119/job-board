1- I Use MySql Server As Database <br>
2- I used Seeders with Factory to Create 20 Job , attributes , categories
,locations and languages <br>

##### 3- Firstly run  ==> composer update <==

##### 4- Secondly run  ==> php artisan migrate <==

##### 5- Thirdly run ==> **php artisan migrate:fresh --seed** <== to generate data <br>

##### 6- Finally don't forget run ==> alias devup='php artisan cache:clear && php artisan route:clear && php artisan config:clear && php artisan view:clear'

#####   then run ==>  devup  <==  to clear cache and route


