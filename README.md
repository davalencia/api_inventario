Documentación del API de Inventario

Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Composer (para gestión de dependencias)
- Servidor web (Apache, Nginx, etc.)
- Postman (para pruebas de API)

Instalación
Clonar el repositorio:
- git clone https://github.com/davalencia/api_inventario.git
- cd api_inventario

Configurar la base de datos:
- Crear una base de datos MySQL llamada api_inventario.
- Importar el archivo SQL ubicado en DB.

Configuración:
- CORS: Configura los encabezados CORS en .htaccess si es necesario.

Estructura del Proyecto:
api_inventario/
  ├── DB/                  # Scripts de base de datos
  ├── config/              # Configuraciones de la aplicación
  ├── controllers/         # Controladores de la API
  ├── models/              # Modelos de la base de datos
  ├── routes/              # Definición de rutas
  ├── utils/               # Utilidades y helpers
  ├── .env                 # Variables de entorno
  ├── .htaccess            # Configuración de Apache
  └── index.php            # Punto de entrada
  
Autenticación:
- La API utiliza autenticación por token JWT (Bearer Token). Para acceder a los endpoints protegidos, debes:
- Iniciar sesión con el endpoint /session/login para obtener un token.
- Incluir el token en el encabezado Authorization: Bearer [token] en cada solicitud.

Endpoints
Login
Método: POST

Ruta: /session/login

Descripción: Inicia sesión y devuelve un token de autenticación.

Body:

json
Copy
{
  "username": "admin",
  "password": "123"
}
Logout
Método: POST

Ruta: /session/logout

Descripción: Cierra la sesión y revoca el token.

Headers:

Authorization: Bearer [token]

session_token: [token_de_sesión]

Body:

json
Copy
{
  "token": "[token_de_sesión]"
}
Productos
Agregar producto
Método: POST

Ruta: /producto

Descripción: Crea un nuevo producto.

Body:

json
Copy
{
  "nombre": "Ejemplo",
  "descripcion": "Descripción del producto",
  "precio": 10000,
  "stock": 5,
  "ubicacion": "Bodega A"
}
Editar producto
Método: PUT

Ruta: /producto/{id}

Descripción: Actualiza un producto existente.

Body:

json
Copy
{
  "nombre": "Producto actualizado",
  "descripcion": "Nueva descripción",
  "precio": 15000,
  "stock": 10,
  "ubicacion": "Bodega B"
}
Listar productos
Método: GET

Ruta: /producto

Descripción: Devuelve una lista de todos los productos.

Listar producto por ID
Método: GET

Ruta: /producto/{id}

Descripción: Devuelve un producto específico por su ID.

Total de productos registrados
Método: GET

Ruta: /producto/count

Descripción: Devuelve el número total de productos.

Listar producto por filtro
Método: POST

Ruta: /producto/option

Descripción: Busca productos según un criterio.

Body:

json
Copy
{
  "search": "criterio"
}
Eliminar producto
Método: DELETE

Ruta: /producto/{id}

Descripción: Elimina un producto por su ID.

Colección de Postman
La colección de Postman incluida (api_inventario.postman_collection.json) contiene ejemplos de todas las solicitudes mencionadas. Importa esta colección en Postman para probar los endpoints fácilmente.

Importar colección:

Abre Postman y haz clic en "Import".

Selecciona el archivo api_inventario.postman_collection.json.

Configurar variables:

Crea un entorno en Postman con la variable base_url apuntando a http://localhost/api_inventario.

Ejemplos de Uso
Iniciar sesión:

bash
Copy
curl -X POST http://localhost/api_inventario/session/login \
-H "Content-Type: application/json" \
-d '{"username":"admin","password":"123"}'
Listar productos:

bash
Copy
curl -X GET http://localhost/api_inventario/producto \
-H "Authorization: Bearer [token]"
¡Gracias por usar el API de Inventario! Para más ayuda, consulta la colección de Postman o abre un issue en el repositorio.
