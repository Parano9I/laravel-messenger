FROM node:18-alpine

WORKDIR /var/www/

COPY --chown=node:node package*.json ./

RUN npm install

RUN chown -R node:node /var/www/

COPY --chown=node:node . .

EXPOSE 3000 3001

CMD ["npm", "run", "dev"]
