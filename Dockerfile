FROM php:8.1-apache

# تثبيت المكتبات المطلوبة
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# نسخ الملفات
COPY . /var/www/html/

# تعيين الأذونات
RUN chown -R www-data:www-data /var/www/html

# تفعيل mod_rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 8080

CMD ["apache2-foreground"]
