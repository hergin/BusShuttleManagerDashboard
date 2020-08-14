FROM mattrayner/lamp:latest-1804

ADD initialize_db.sh /initialize_db.sh
RUN cat /run.sh | head -n -2 > /newrun.sh && \
    echo "/initialize_db_unix.sh &" >> /newrun.sh && \
    echo "echo 'Starting supervisord'" >> /newrun.sh && \
    echo "exec supervisord -n" >> /newrun.sh && \
    chmod +x /newrun.sh && \
    tr -d '\15\32' < /initialize_db.sh > /initialize_db_unix.sh && \
    chmod +x /initialize_db_unix.sh

CMD ["/newrun.sh"]
