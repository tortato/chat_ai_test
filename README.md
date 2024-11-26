# Laravel AI Chat Application

This is a Laravel-based application designed to manage user chat history and integrate with an AI service to provide real-time streaming responses. The application focuses on the energy market-related queries and adheres to clean architecture principles.

---

## **Features**
- CRUD operations for chat history.
- AI integration with real-time streaming responses (\`text/event-stream\`).
- Focused filtering for energy market-related information.
- Secure API endpoints.
- Dockerized environment for easy setup.
- Features tested using PHPUnit.
- API documentation using Swagger.

---

## **Requirements**
- Docker and Docker Compose.

---

## **Getting Started**

### **1. Clone the Repository**
```bash
git clone https://github.com/tortato/chat_ai_test.git laravel-chat-app
cd laravel-chat-app
```

### **2. Set Up Environment**
Copy the `.env.example` file and update the environment variables:
```bash
cp .env.example .env
```
Ensure the database and API settings are configured properly:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```
Configure with your OpenAI credential and the AI Model:
```env
OPENAI_API_KEY=
OPENAI_API_MODEL=
```

### **3. Run Docker and Laravel**
Build and start the Docker containers:
```bash
docker-compose up --build
```
Generate Laravel key for this app:
```bash
docker exec -it chat_app php artisan generate:key
```
Access the application at: [http://localhost:8000](http://localhost:8000)

### **4. Run Migrations**
Run the migrations to set up the database:
```bash
docker exec -it chat_app php artisan migrate
```

---

## **API Documentation**

The API is documented using Swagger. To access the documentation:
1. Start the application.
2. Navigate to: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

---

## **Run Tests**

To run PHPUnit tests for the application:
1. Ensure the application is running in Docker.
2. Execute the following command:
   ```bash
   docker exec -it laravel_app php artisan test
   ```

---

## **Project Structure**

- **Controllers:** Handle API logic (\`ChatController\`, \`AuthController\`).
- **Services:** Abstract business logic (e.g., \`ChatService\`, \`AIService\`).
- **Models:** Represent data entities (\`Chat\`, \`User\`).
- **Factories:** Generate test data for models (\`ChatFactory\`).
- **Middleware:** Handle authentication (\`auth:sanctum\`).
- **Swagger Documentation:** Available for all API endpoints.

---

## **How to Use**

### **CRUD Endpoints**
- **List Chats:** \`GET /api/chats\`
- **Create Chat:** \`POST /api/chats\`
- **Retrieve Chat:** \`GET /api/chats/{id}\`
- **Update Chat:** \`PUT /api/chats/{id}\`
- **Delete Chat:** \`DELETE /api/chats/{id}\`

### **Authentication**
- **Generate Token:** \`POST /api/auth/token\` (requires email and password).
- **Logout:** \`POST /api/auth/logout\` (requires valid token).

---

## **Docker Services**
- **App Container:** Runs the Laravel application.
- **Database Container:** MySQL 8.0 for data storage.
