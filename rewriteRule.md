Nginx ReriteRule
===================
location / {
  if(!-f $request_filename){
    rewrite (.*) /index.php;
  }
}