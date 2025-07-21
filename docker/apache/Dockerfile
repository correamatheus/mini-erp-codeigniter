FROM php:7.4-apache

# Instalações básicas
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# COPIA OS ARQUIVOS DE CONFIGURAÇÃO DO APACHE
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilita o módulo rewrite e o site padrão
RUN a2enmod rewrite && \
    a2ensite 000-default.conf && \
    # Garante que o Apache respeite o .htaccess no DocumentRoot
    # Esta linha é uma garantia extra, pode ser útil se houver conflito global
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    chown -R www-data:www-data /var/www/html/ && \
    chmod -R 755 /var/www/html/

# Expondo a porta 80 do container
EXPOSE 80