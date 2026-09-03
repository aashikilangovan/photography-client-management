FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 5173

# Vite's dev server, not a production build — appropriate for local dev /
# a portfolio project. --host is required so it's reachable from outside
# the container, not just localhost inside it.
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
