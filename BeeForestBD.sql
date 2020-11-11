-- ------------------------------------------------------------------------ --
--                        BASE DE DATOS BEE FOREST                          --
-- ------------------------------------------------------------------------ --

USE MASTER
GO

PRINT 'Borrando base de datos BeeForest si existe'
GO

IF (exists (SELECT * FROM sysdatabases WHERE NAME='BeeForestBD'))
	DROP DATABASE BeeForestBD
GO

PRINT 'Creando base de datos BeeForest'
GO

CREATE DATABASE BeeForestBD ON  PRIMARY (
	NAME = N'beeforestbd',
	FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL15.MSSQLSERVER\MSSQL\DATA\beeforestbd.mdf' ,
	SIZE = 8192KB ,
	MAXSIZE = UNLIMITED,
	FILEGROWTH = 65536KB )
LOG ON (
	NAME = N'beeforestbd_log',
	FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL15.MSSQLSERVER\MSSQL\DATA\beeforestbd_log.ldf' ,
	SIZE = 8192KB ,
	MAXSIZE = 2048GB ,
	FILEGROWTH = 65536KB )
 WITH CATALOG_COLLATION = DATABASE_DEFAULT
GO

USE BeeForestBD
GO

-- ------------------------------------------------------------------------ --
--                           CREACION DE TABLAS                             --
-- ------------------------------------------------------------------------ --

PRINT 'Creando tabla categories'
GO

CREATE TABLE categories(
	id BIGINT IDENTITY(1,1) NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	[description] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT categories_pk
	PRIMARY KEY (id)
)
GO

PRINT 'Creando tabla clients'
GO

CREATE TABLE clients(
	id UNIQUEIDENTIFIER NOT NULL,
	identificationCard NVARCHAR(255) NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	surname NVARCHAR(255) NOT NULL,
	telephone NVARCHAR(255) NOT NULL,
	email NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT clients_pk
	PRIMARY KEY (id)
)

GO

PRINT 'Creando tabla collaborators'
GO

CREATE TABLE collaborators(
	id UNIQUEIDENTIFIER NOT NULL,
	username NVARCHAR(255) NOT NULL,
	[password] NVARCHAR(255) NOT NULL,
	email NVARCHAR(255) NOT NULL,
	[role] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

    CONSTRAINT collaborators_pk
	PRIMARY KEY (id)
)

GO

PRINT 'Creando tabla directions'
GO

CREATE TABLE directions(
	id BIGINT IDENTITY(1,1) NOT NULL,
	clientId UNIQUEIDENTIFIER NOT NULL,
	country NVARCHAR(255) NOT NULL,
	province NVARCHAR(255) NOT NULL,
	city NVARCHAR(255) NOT NULL,
	zipCode NVARCHAR(255) NOT NULL,
	direction NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT directions_pk
	PRIMARY KEY (id),

	CONSTRAINT directions_clients_fk
	FOREIGN KEY (clientId) REFERENCES clients
)

GO

PRINT 'Creando tabla products'
GO

CREATE TABLE products(
	id BIGINT IDENTITY(1,1) NOT NULL,
	categoryId BIGINT NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	price FLOAT NOT NULL,
	amount INT NOT NULL,
	[description] NVARCHAR(255) NOT NULL,
	[image] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT products_pk
	PRIMARY KEY (id),

	CONSTRAINT products_categories_fk
	FOREIGN KEY (categoryId) REFERENCES categories
)

GO

PRINT 'Creando tabla shippings'
GO

CREATE TABLE shippings(
	id BIGINT IDENTITY(1,1) NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	price FLOAT NOT NULL,
	[description] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT shippings_pk
	PRIMARY KEY (id)
)

GO

PRINT 'Creando tabla orders'
GO

CREATE TABLE orders(
	id BIGINT IDENTITY(1,1) NOT NULL,
	collaboratorId UNIQUEIDENTIFIER NOT NULL,
	clientId UNIQUEIDENTIFIER NOT NULL,
	ShippingId BIGINT NOT NULL,
	creationDate DATETIME NOT NULL,
	deliveryDate DATETIME NOT NULL,
	discount INT NOT NULL,
	totalPrice FLOAT NOT NULL,
	[status] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT orders_pk
	PRIMARY KEY (id),

	CONSTRAINT orders_collaborators_fk
	FOREIGN KEY (collaboratorId) REFERENCES collaborators,

	CONSTRAINT orders_clients_fk
	FOREIGN KEY (clientId) REFERENCES clients,

	CONSTRAINT orders_shippings_fk
	FOREIGN KEY (ShippingId) REFERENCES shippings
)

GO

PRINT 'Creando tabla providers'
GO

CREATE TABLE providers(
	id UNIQUEIDENTIFIER NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	surname NVARCHAR(255) NOT NULL,
	telephone NVARCHAR(255) NOT NULL,
	direction NVARCHAR(255) NOT NULL,
	email NVARCHAR(255) NOT NULL,
	startDay NVARCHAR(255) NOT NULL,
	finalDay NVARCHAR(255) NOT NULL,
	StartTime NVARCHAR(255) NOT NULL,
	finalTime NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT providers_pk
	PRIMARY KEY (id)
)

GO

PRINT 'Creando tabla materials'
GO

CREATE TABLE materials(
	id BIGINT IDENTITY(1,1) NOT NULL,
	providerId UNIQUEIDENTIFIER NOT NULL,
	[name] NVARCHAR(255) NOT NULL,
	price FLOAT NOT NULL,
	amount INT NOT NULL,
	[description] NVARCHAR(255) NOT NULL,
	[image] NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT materials_pk
	PRIMARY KEY (id),

	CONSTRAINT materials_providers_fk
	FOREIGN KEY (providerId) REFERENCES providers
)

GO

PRINT 'Creando tabla product_material'
GO

CREATE TABLE product_material(
	id BIGINT IDENTITY(1,1) NOT NULL,
	productId BIGINT NOT NULL,
	materialId BIGINT NOT NULL,
	amount INT NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT product_material_pk
	PRIMARY KEY (id),

	CONSTRAINT product_material_products_fk
	FOREIGN KEY (productId) REFERENCES products,

	CONSTRAINT product_material_materials_fk
	FOREIGN KEY (materialId) REFERENCES materials
)

GO

PRINT 'Creando tabla product_order'
GO

CREATE TABLE product_order(
	id BIGINT IDENTITY(1,1) NOT NULL,
	productId BIGINT NOT NULL,
	orderId BIGINT NOT NULL,
	amount INT NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT product_order_pk
	PRIMARY KEY (id),

	CONSTRAINT product_order_products_fk
	FOREIGN KEY (productId) REFERENCES products,

	CONSTRAINT product_order_orders_fk
	FOREIGN KEY (orderId) REFERENCES orders
)

GO

PRINT 'Creando tabla refunds'
GO

CREATE TABLE refunds(
	id BIGINT IDENTITY(1,1) NOT NULL,
	refundDate DATE NOT NULL,
	orderId BIGINT NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT refunds_pk
	PRIMARY KEY (id),

	CONSTRAINT refunds_orders_fk
	FOREIGN KEY (orderId) REFERENCES orders
)

GO

PRINT 'Creando tabla product_refund'
GO

CREATE TABLE product_refund(
	id BIGINT IDENTITY(1,1) NOT NULL,
	productId BIGINT NOT NULL,
	refundId BIGINT NOT NULL,
	amount INT NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,

	CONSTRAINT product_refund_pk
	PRIMARY KEY (id),

	CONSTRAINT product_refund_products_fk
	FOREIGN KEY (productId) REFERENCES products,

	CONSTRAINT product_refund_refunds_fk
	FOREIGN KEY (refundId) REFERENCES refunds
)

GO

-- ------------------------------------------------------------------------ --
--                                AUDITORÍA                                 --
-- ------------------------------------------------------------------------ --

PRINT 'Creando tablas de Auditoria'
GO

CREATE TABLE audit_orders(
	id BIGINT IDENTITY(1,1) NOT NULL,
	userAudit NVARCHAR(255) NULL,
	orderId BIGINT NOT NULL,
	shippingOld BIGINT NULL,
	shippingNew BIGINT NOT NULL,
	action NVARCHAR(255) NOT NULL,
	statusOld NVARCHAR(255) NULL,
	statusNew NVARCHAR(255) NOT NULL,
	created_at DATETIME NULL,

	CONSTRAINT audit_orders_pk
	PRIMARY KEY (id),
)
GO

CREATE TABLE audit_collaborators(
	id BIGINT IDENTITY(1,1) NOT NULL,
	userAudit NVARCHAR(255) NULL,
	collaboratorEditedId UNIQUEIDENTIFIER NOT NULL,
	action NVARCHAR(255) NOT NULL,
	usernameOld NVARCHAR(255) NULL,
	usernameNew NVARCHAR(255) NOT NULL,
	emailOld NVARCHAR(255) NULL,
	emailNew NVARCHAR(255) NOT NULL,
	created_at DATETIME NOT NULL,

	CONSTRAINT audit_collaborators_pk
	PRIMARY KEY (id),
)
GO

-- ------------------------------------------------------------------------ --
--                   CREACION DE USUARIO ADMINISTRADOR                      --
-- ------------------------------------------------------------------------ --

PRINT 'Creando usuario administrador en la tabla Colaboradores'
GO

INSERT INTO collaborators VALUES
	('FF605560-E5BC-11EA-8530-EF0B356FE153','admin','FF605560-E5BC-11EA-8530-EF0B356FE153','admin@admin.com','admin',
	'2020-08-23 21:50:48.970','2020-08-23 21:50:48.970')

-- ------------------------------------------------------------------------ --
--                      CREACION DE USUARIOS DE LA BD                       --
-- ------------------------------------------------------------------------ --

PRINT 'Creando usuarios de la BD'
GO

SP_ADDLOGIN 'BeeAdmin', 'BeeAdmin', 'master'
GO

SP_ADDUSER 'BeeAdmin', 'BeeAdmin'
GO

-- Permisos
SP_ADDSRVROLEMEMBER 'BeeAdmin', 'sysadmin'
GO

-- ------------------------------------------------------------------------ --
--                           CREACION DE VISTAS                             --
-- ------------------------------------------------------------------------ --

PRINT 'Creando vista de productos y categorias'
GO

CREATE VIEW v_listProductsCategories
AS
(
	SELECT *, (SELECT name
			   FROM categories
			   WHERE categories.id = products.categoryId) AS 'categoryName'
	FROM products
)

GO

PRINT 'Creando vista de materiales y proveedores'
GO

CREATE VIEW v_listMaterialsProviders
AS
(
	SELECT *, (SELECT name
			   FROM providers
			   WHERE providers.id = materials.providerId) AS 'providerName'
	FROM materials
)
GO

PRINT 'Creando vista de devoluciones realizadas, con su cliente y productos asociados'
GO

CREATE VIEW v_listRefundsProductsClients
AS
(
	SELECT refundDate,
		   (SELECT name
				FROM products
				WHERE products.id = product_refund.productId) AS 'productName',
		   (SELECT name
				FROM clients
				WHERE clients.id = (SELECT clientId
                                    FROM orders
                                    WHERE orders.id = refunds.id)) AS 'clientName'
	FROM refunds
	INNER JOIN product_refund ON product_refund.refundId = refunds.id
)

GO

PRINT 'Creando vista de detalle de pedidos'
GO

CREATE VIEW v_listOrdersOrdersClientsDirections
AS
(
	SELECT orders.id, deliveryDate, totalPrice, status,
		   (SELECT username
		    FROM collaborators
			WHERE orders.collaboratorId = collaborators.id) AS 'collaboratorName',
			clients.name AS 'clientName',
			shippings.name AS 'shippingName',
			shippings.price AS 'shippingPrice',
			shippings.description AS 'shippingDescription'
	FROM orders
	INNER JOIN clients ON clients.id = orders.clientId
	INNER JOIN shippings ON orders.ShippingId = shippings.id
)

GO

PRINT 'Creando vista de pocos materiales'
GO

CREATE VIEW v_listLowMaterials
AS
(
	SELECT *
	FROM materials
	WHERE amount < 10
)

GO

PRINT 'Creando vista de pocos productos'
GO

CREATE VIEW v_listLowProducts
AS
(
	SELECT *
	FROM products
	WHERE amount < 10
)

GO

-- ------------------------------------------------------------------------ --
--                  CREACION DE PROCEDIMIENTOS ALMACENADOS                  --
-- ------------------------------------------------------------------------ --

PRINT 'Creando procedimientos almacenados CRUD para la tabla Providers'
GO

-- INSERT
CREATE PROCEDURE pa_addProvider
	@id UNIQUEIDENTIFIER,
	@name NVARCHAR(255),
	@surname NVARCHAR(255),
	@telephone NVARCHAR(255),
	@direction NVARCHAR(255),
	@email NVARCHAR(255),
    @startDay NVARCHAR(255),
    @finalDay NVARCHAR(255),
    @StartTime NVARCHAR(255),
    @finalTime NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@id IS NULL OR @name IS NULL OR @surname IS NULL OR @telephone IS NULL OR @direction
        IS NULL OR @email IS NULL OR @startDay IS NULL OR @finalDay IS NULL OR @StartTime
        IS NULL OR @finalTime IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación ID o Email ya existentes
        IF NOT EXISTS(SELECT id, email
                      FROM providers
                      WHERE id = @id OR email = @email)
            -- Insertar
	        INSERT INTO providers (id,name,surname,telephone,direction,email,startDay,
						finalDay,StartTime,finalTime,created_at,updated_at)
	        VALUES (@id,@name,@surname,@telephone,@direction,@email,@startDay,
			        @finalDay,@StartTime,@finalTime,@created_at,@updated_at)
        ELSE
	        SELECT 'Tu cedula o correo electrónico ya se encuentran registrados' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readProviders
AS
BEGIN
	SELECT * FROM providers
END

GO

-- SELECT BY EMAIL
CREATE PROCEDURE pa_selectProviderByEmail
	@email NVARCHAR(255)
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM providers
                  WHERE email = @email)
            SELECT 'El Correo Electrónico del Proveedor no existe' AS status;
        -- Actualizar
        ELSE
        	SELECT * FROM providers WHERE email = @email
END

GO

-- SELECT BY ID
CREATE PROCEDURE pa_selectProviderById
	@id UNIQUEIDENTIFIER
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM providers
                  WHERE id = @id)
            SELECT 'El ID del Proveedor no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM providers WHERE id = @id
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateProvider
	@id UNIQUEIDENTIFIER,
	@name NVARCHAR(255),
	@surname NVARCHAR(255),
	@telephone NVARCHAR(255),
	@direction NVARCHAR(255),
	@email NVARCHAR(255),
    @startDay NVARCHAR(255),
    @finalDay NVARCHAR(255),
    @StartTime NVARCHAR(255),
    @finalTime NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@id IS NULL OR @name IS NULL OR @surname IS NULL OR @telephone IS NULL OR @direction
        IS NULL OR @email IS NULL OR @startDay IS NULL OR @finalDay IS NULL OR @StartTime
        IS NULL OR @finalTime IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación ID no existe
        IF NOT EXISTS(SELECT id
                      FROM providers
                      WHERE id = @id)
            SELECT 'El ID del Proveedor no existe' AS status;
        -- Actualizar
        ELSE
	        UPDATE providers SET name = @name,surname = @surname,telephone = @telephone,direction = @direction,
						  email = @email,startDay = @startDay,finalDay = @finalDay,StartTime = @StartTime,
						  finalTime = @finalTime,updated_at = @updated_at
			        WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteProvider
	@id UNIQUEIDENTIFIER
AS
BEGIN
    -- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM providers
                  WHERE id = @id)
            SELECT 'El ID del Proveedor no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM providers WHERE id = @id
END

GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Materials'
GO

-- INSERT
CREATE PROCEDURE pa_addMaterial
	@providerId UNIQUEIDENTIFIER,
	@name NVARCHAR(255),
	@price FLOAT,
	@description NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
	-- Validación nulos
    IF (@providerId IS NULL OR @name IS NULL OR @price IS NULL OR @description IS NULL OR @created_at
        IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación Proveedor existe
        IF EXISTS(SELECT id
                  FROM providers
                  WHERE id = @providerId)
            -- Insertar
	        INSERT INTO materials (providerId, name, price, amount, description, image, created_at, updated_at)
	        VALUES (@providerId, @name, @price, 0, @description,'No image', @created_at, @updated_at)
        ELSE
	        SELECT 'El ID del Proveedor no existe' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readMaterials
AS
BEGIN
	SELECT * FROM materials
END

GO

-- SELECT
CREATE PROCEDURE pa_selectMaterial
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM materials
                  WHERE id = @id)
            SELECT 'El ID del Material no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM materials WHERE id = @id
END

GO

-- SELECT
CREATE PROCEDURE pa_selectMaterialByName
	@name NVARCHAR(255)
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM clients
                  WHERE name = @name)
            SELECT 'El nombre del Material no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM materials WHERE name = @name
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateMaterial
	@id BIGINT,
	@providerId UNIQUEIDENTIFIER,
	@name NVARCHAR(255),
	@price FLOAT,
	@amount INT,
    @description NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE materials SET providerId = @providerId, name = @name,price = @price,amount = @amount,description = @description, updated_at = @updated_at
			WHERE id = @id;
END

GO

-- UPDATE MORE MATERIAL
CREATE PROCEDURE pa_updateMoreAmountMaterial
	@id BIGINT,
	@amount INT,
	@updated_at DATETIME
AS
BEGIN
    IF(@amount > 0)
    BEGIN
	    UPDATE materials SET amount = (amount + @amount), updated_at = @updated_at
			    WHERE id = @id;
    END
    ELSE
    BEGIN
        SELECT 'La cantidad a añadir no puede ser menor a 1' AS status
    END
END

GO

-- UPDATE LESS MATERIAL
CREATE PROCEDURE pa_updateLessAmountMaterial
	@id BIGINT,
	@lessAmount INT,
	@updated_at DATETIME
AS
BEGIN
    IF(@lessAmount > 0)
    BEGIN
	    UPDATE materials SET amount = (amount - @lessAmount), updated_at = @updated_at
			    WHERE id = @id;
    END
    ELSE
    BEGIN
        SELECT 'La cantidad a utilizar no puede ser menor a 1' AS status
    END
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteMaterial
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM materials
                  WHERE id = @id)
            SELECT 'El ID del Material no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM materials WHERE id = @id
END

GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Products'
GO

-- INSERT
CREATE PROCEDURE pa_addProduct
	@categoryId BIGINT,
	@name NVARCHAR(255),
	@price FLOAT,
	@description NVARCHAR(255),
	@image NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@categoryId IS NULL OR @name IS NULL OR @price IS NULL OR @description IS NULL OR @image
        IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación Categoria existe
        IF EXISTS(SELECT id
                  FROM categories
                  WHERE id = @categoryId)
            -- Insertar
	        INSERT INTO products (categoryId, name, price, amount, description, image, created_at, updated_at)
	        VALUES (@categoryId, @name, @price, 0, @description, @image, @created_at, @updated_at)
        ELSE
	        SELECT 'El ID de la Categoria no existe' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readProducts
AS
BEGIN
	SELECT * FROM products
END

GO

-- SELECT
CREATE PROCEDURE pa_selectProduct
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM products
                  WHERE id = @id)
            SELECT 'El ID del Producto no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM products WHERE id = @id
END

GO

-- SELECT PRODUCT BY NAME
CREATE PROCEDURE pa_selectProductByName
	@name NVARCHAR(255)
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM clients
                  WHERE name = @name)
            SELECT 'El ID del Proveedor no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM products WHERE name = @name
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateProduct
	@id BIGINT,
	@categoryId BIGINT,
	@name NVARCHAR(255),
	@price FLOAT,
	@amount INT,
	@description NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE products SET name = @name, categoryId = @categoryId, price = @price,
								amount = @amount, description = @description, updated_at = @updated_at
			WHERE id = @id;
END

GO

-- UPDATE LESS PRODUCT
CREATE PROCEDURE pa_updateLessAmountProduct
	@id BIGINT,
	@amount INT,
	@updated_at DATETIME
AS
BEGIN
    IF(@amount > 0)
    BEGIN
	    UPDATE products SET amount = (amount - @amount), updated_at = @updated_at
			    WHERE id = @id;
    END
    ELSE
    BEGIN
        SELECT 'La cantidad a utilizar no puede ser menor a 1' AS status
    END
END

GO

-- UPDATE MORE PRODUCT
CREATE PROCEDURE pa_updateMoreAmountProduct
	@id BIGINT,
	@amount INT,
	@updated_at DATETIME
AS
BEGIN
    IF(@amount > 0)
    BEGIN
	    UPDATE products SET amount = (amount + @amount), updated_at = @updated_at
			    WHERE id = @id;
    END
    ELSE
    BEGIN
        SELECT 'La cantidad a añadir no puede ser menor a 1' AS status
    END
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteProduct
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM products
                  WHERE id = @id)
            SELECT 'El ID del Producto no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM products WHERE id = @id
END

GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Categories'
GO

-- INSERT
CREATE PROCEDURE pa_addCategory
	@name NVARCHAR(255),
	@description NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@name IS NULL OR @description IS NULL OR @description IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Insertar
	    INSERT INTO categories(name, description, created_at, updated_at)
	    VALUES (@name, @description, @created_at, @updated_at)
END

GO

-- READ
CREATE PROCEDURE pa_readCategories
AS
BEGIN
	SELECT * FROM categories
END

GO

-- SELECT
CREATE PROCEDURE pa_selectCategory
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM categories
                  WHERE id = @id)
            SELECT 'El ID de la Categoria no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM categories WHERE id = @id
END

GO

-- SELECT CATEGORY BY NAME
CREATE PROCEDURE pa_selectCategoryByName
	@name NVARCHAR(255)
AS
BEGIN
	SELECT * FROM categories WHERE name = @name
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateCategory
	@id BIGINT,
	@name NVARCHAR(255),
	@description NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE categories SET name = @name,description = @description,
			updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteCategory
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM categories
                  WHERE id = @id)
            SELECT 'El ID de la Categoria no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM categories WHERE id = @id
END

GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Orders'
GO

-- INSERT
CREATE PROCEDURE pa_addOrder
	@collaboratorId UNIQUEIDENTIFIER,
	@clientId UNIQUEIDENTIFIER,
	@ShippingId BIGINT,
	@creationDate DATETIME,
	@deliveryDate DATETIME,
	@discount INT,
	@totalPrice FLOAT,
	@status NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@collaboratorId IS NULL OR @clientId IS NULL OR @ShippingId IS NULL OR @creationDate IS NULL OR @deliveryDate
        IS NULL OR @discount IS NULL OR @totalPrice IS NULL OR @status IS NULL OR @created_at
        IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación Colaborador existe
        IF (EXISTS(SELECT id
                  FROM collaborators
                  WHERE id = @collaboratorId)
            AND
            EXISTS(SELECT id
                  FROM clients
                  WHERE id = @clientId)
            AND
            EXISTS(SELECT id
                  FROM shippings
                  WHERE id = @ShippingId))
            -- Insertar
	        INSERT INTO orders (collaboratorId, clientId, ShippingId, creationDate, deliveryDate, discount, totalPrice, status, created_at, updated_at)
	        VALUES (@collaboratorId, @clientId, @ShippingId, @creationDate, @deliveryDate, @discount, @totalPrice, @status, @created_at, @updated_at)
        ELSE
	        SELECT 'El ID del Colaborador, Cliente o Tipo de Envio no existe' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readOrders
AS
BEGIN
	SELECT * FROM orders
END

GO

-- SELECT
CREATE PROCEDURE pa_selectOrder
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM orders
                  WHERE id = @id)
            SELECT 'El ID del Pedido no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM orders WHERE id = @id
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateOrder
	@id BIGINT,
	@collaboratorId UNIQUEIDENTIFIER,
	@clientId UNIQUEIDENTIFIER,
	@shippingId BIGINT,
	@discount INT,
	@totalPrice FLOAT,
	@status NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE orders SET collaboratorId = @collaboratorId,clientId = @clientId,
			ShippingId = @shippingId, totalPrice = @totalPrice,
			status = @status,updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteOrder
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM orders
                  WHERE id = @id)
            SELECT 'El ID del Pedido no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM orders WHERE id = @id
END
GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Refunds'
GO

-- INSERT
CREATE PROCEDURE pa_addRefund
	@refundDate DATE,
	@orderId BIGINT,
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@refundDate IS NULL OR @orderId IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación Categoria existe
        IF EXISTS(SELECT id
                  FROM orders
                  WHERE id = @orderId)
            -- Insertar
	        INSERT INTO refunds (refundDate, orderId, created_at, updated_at)
	        VALUES (@refundDate, @orderId, @created_at, @updated_at)
        ELSE
	        SELECT 'El ID del Pedido no existe' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readRefunds
AS
BEGIN
	SELECT * FROM refunds
END

GO

-- SELECT
CREATE PROCEDURE pa_selectRefund
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM refunds
                  WHERE id = @id)
            SELECT 'El ID de la Devolucion no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM refunds WHERE id = @id
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateRefund
	@id BIGINT,
	@refundDate DATE,
	@orderId BIGINT,
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
	UPDATE refunds SET refundDate = @refundDate, orderId = @orderId ,updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteRefund
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM orders
                  WHERE id = @id)
            SELECT 'El ID de la Devolucion no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM refunds WHERE id = @id
END
GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Collaborators'
GO

-- INSERT
CREATE PROCEDURE pa_addCollaborator
	@id UNIQUEIDENTIFIER,
	@username NVARCHAR(255),
	@password NVARCHAR(255),
	@email NVARCHAR(255),
	@role NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@id IS NULL OR @username IS NULL OR @password IS NULL OR @email IS NULL OR @role
        IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación ID, username o Email ya existentes
        IF NOT EXISTS(SELECT id, username, email
                      FROM collaborators
                      WHERE id = @id OR username = @username OR email = @email)
            -- Insertar
	        INSERT INTO collaborators (id, username, password, email, role, created_at, updated_at)
	        VALUES (@id, @username, @password, @email, @role, @created_at, @updated_at)
        ELSE
	        SELECT 'El ID, nombre de usuario o correo electrónico ya se encuentran registrados' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readCollaborators
AS
BEGIN
	SELECT id, username, email, role FROM collaborators WHERE role = 'standart'
END

GO

-- SELECT
CREATE PROCEDURE pa_selectCollaborator
	@id UNIQUEIDENTIFIER
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM clients
                  WHERE id = @id)
            SELECT 'El ID del Colaborador no existe' AS status;
        -- Select
        ELSE
        	SELECT id, username, email, role FROM collaborators WHERE id = @id
END

GO

-- SELECT USERNAME
CREATE PROCEDURE pa_selectUserNameCollaborator
	@username NVARCHAR(255)
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM collaborators
                  WHERE username = @username)
            SELECT 'El Nombre de Usuario del Colaborador no existe' AS status;
        -- Select
        ELSE
        	SELECT id, username, email, role FROM collaborators WHERE username = @username
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateCollaborator
	@id UNIQUEIDENTIFIER,
	@username NVARCHAR(255),
	@password NVARCHAR(255),
	@email NVARCHAR(255),
	@role NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE collaborators SET username = @username, password = @password, email = @email, role = @role, updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteCollaborator
	@id UNIQUEIDENTIFIER
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM collaborators
                  WHERE id = @id)
            SELECT 'El ID del Colaborador no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM collaborators WHERE id = @id
END
GO

PRINT 'Procedimientos almacenado Cambiar Contraseña'
GO

CREATE PROCEDURE pa_changePassword
	@id UNIQUEIDENTIFIER,
	@password NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE collaborators SET password = @password, updated_at = @updated_at
			WHERE id = @id;
END

GO

PRINT 'Creando procedimientos almacenados CRUD para la tabla Shippings'
GO

-- INSERT
CREATE PROCEDURE pa_addShipping
	@name NVARCHAR(255),
	@price FLOAT,
	@description NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@name IS NULL OR @price IS NULL OR @description IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación name ya existente
        IF NOT EXISTS(SELECT name
                      FROM shippings
                      WHERE name = @name)
            -- Insertar
	        INSERT INTO shippings (name, price, description, created_at, updated_at)
	        VALUES (@name, @price, @description, @created_at, @updated_at)
        ELSE
	        SELECT 'El Nombre ya se encuentra registrado' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readShippings
AS
BEGIN
	SELECT * FROM shippings
END

GO

-- SELECT
CREATE PROCEDURE pa_selectShipping
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM shippings
                  WHERE id = @id)
            SELECT 'El ID del Tipo de Envio no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM shippings WHERE id = @id
END

GO

--SELECT SHIPPING BY NAME
CREATE PROCEDURE pa_selectShippingByName
	@name NVARCHAR(255)
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM shippings
                  WHERE name = @name)
            SELECT 'El Nombre del Tipo de Envio no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM shippings WHERE name = @name
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateShipping
	@id BIGINT,
	@name NVARCHAR(255),
	@price FLOAT,
	@description NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
	UPDATE shippings SET name = @name, price = @price, description = @description, updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteShipping
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM shippings
                  WHERE id = @id)
            SELECT 'El ID del Tipo de Envio no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM shippings WHERE id = @id
END
GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Clients'
GO

-- INSERT
CREATE PROCEDURE pa_addClient
	@id UNIQUEIDENTIFIER,
	@identificationCard NVARCHAR(255),
	@name NVARCHAR(255),
	@surname NVARCHAR(255),
	@telephone NVARCHAR(255),
	@email NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@id IS NULL OR @identificationCard IS NULL OR @name IS NULL OR  @surname IS NULL OR @telephone
        IS NULL OR @email IS NULL OR @created_at  IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación ID, Cédula o Email ya existentes
        IF NOT EXISTS(SELECT id, identificationCard, email
                      FROM clients
                      WHERE id = @id OR identificationCard = @identificationCard OR email = @email)
            -- Insertar
	        INSERT INTO clients (id, identificationCard, name, surname, telephone, email, created_at, updated_at)
	        VALUES (@id, @identificationCard, @name, @surname, @telephone, @email, @created_at, @updated_at)
        ELSE
	        SELECT 'Tu cedula o correo electrónico ya se encuentran registrados' AS status;
END

GO

-- READ
CREATE PROCEDURE pa_readClients
AS
BEGIN
	SELECT * FROM clients
END

GO

-- SELECT
CREATE PROCEDURE pa_selectClient
	@id UNIQUEIDENTIFIER
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM clients
                  WHERE id = @id)
            SELECT 'El ID del Cliente no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM clients WHERE id = @id
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateClient
	@id UNIQUEIDENTIFIER,
	@identificationCard NVARCHAR(255),
	@name NVARCHAR(255),
	@surname NVARCHAR(255),
	@telephone NVARCHAR(255),
	@email NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
	UPDATE clients SET identificationCard = @identificationCard, name = @name, surname = @surname,
						telephone = @telephone, email = @email, updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteClient
	@id UNIQUEIDENTIFIER
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM clients
                  WHERE id = @id)
            SELECT 'El ID del Cliente no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM clients WHERE id = @id
END
GO


PRINT 'Creando procedimientos almacenados CRUD para la tabla Directions'
GO

-- INSERT
CREATE PROCEDURE pa_addDirection
	@clientId UNIQUEIDENTIFIER,
	@country NVARCHAR(255),
	@province NVARCHAR(255),
	@city NVARCHAR(255),
	@zipCode NVARCHAR(255),
	@direction NVARCHAR(255),
	@created_at DATETIME,
	@updated_at DATETIME
AS
BEGIN
    -- Validación nulos
    IF (@clientId IS NULL OR @country IS NULL OR @province IS NULL OR @city IS NULL OR @zipCode IS NULL OR @direction
        IS NULL OR @created_at IS NULL OR @updated_at IS NULL)
        SELECT 'Error, hay dato(s) vacíos' AS status;

    ELSE
        -- Validación cliente existe
        IF EXISTS(SELECT id
                  FROM clients
                  WHERE id = @clientId)
            -- Insertar
	        INSERT INTO directions (clientId, country, province, city, zipCode, direction, created_at, updated_at)
	        VALUES (@clientId, @country, @province, @city, @zipCode, @direction, @created_at, @updated_at)
        ELSE
	        SELECT 'El ID del Cliente no existe' AS status;

END

GO

-- READ
CREATE PROCEDURE pa_readDirections
	@clientId UNIQUEIDENTIFIER
AS
BEGIN
	SELECT * FROM directions WHERE clientId = @clientId
END

GO

-- SELECT
CREATE PROCEDURE pa_selectDirection
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM directions
                  WHERE id = @id)
            SELECT 'El ID de la Direccion no existe' AS status;
        -- Select
        ELSE
        	SELECT * FROM directions WHERE id = @id
END

GO

-- UPDATE
CREATE PROCEDURE pa_updateDirection
	@id BIGINT,
	@clientId UNIQUEIDENTIFIER,
	@country NVARCHAR(255),
	@province NVARCHAR(255),
	@city NVARCHAR(255),
	@zipCode NVARCHAR(255),
	@direction NVARCHAR(255),
	@updated_at DATETIME
AS
BEGIN
	UPDATE directions SET clientId = @clientId, country = @country,
			province = @province, city = @city, zipCode = @zipCode,
			direction = @direction,updated_at = @updated_at
			WHERE id = @id;
END

GO

-- DELETE
CREATE PROCEDURE pa_deleteDirection
	@id BIGINT
AS
BEGIN
	-- Validación ID existe
    IF NOT EXISTS(SELECT id
                  FROM directions
                  WHERE id = @id)
            SELECT 'El ID de la Direccion no existe' AS status;
    -- Eliminar
    ELSE
        DELETE FROM directions WHERE id = @id
END

GO

-- ------------------------------------------------------------------------ --
--                          CREACION DE FUNCIONES                           --
-- ------------------------------------------------------------------------ --

PRINT 'Creando Funciones'
GO

PRINT 'Creando funcion de productos ordenados por categoria'
GO

CREATE FUNCTION f_ProductsByCategory (@categoria INT)
	RETURNS @products TABLE(
	id BIGINT,
	nombre VARCHAR(255),
	precio FLOAT,
	cantidad INT
)
AS
BEGIN
	INSERT @products
		SELECT id,name,price,amount
		FROM products
		WHERE categoryId = @categoria
	RETURN
END;

GO

PRINT 'Creando funcion de pocos productos'
GO

CREATE FUNCTION f_fewProducts
	(@cantidad INT = 20)
	RETURNS TABLE
AS
RETURN(
	SELECT *
	FROM products
	WHERE amount < @cantidad
);

GO

PRINT 'Creando funcion de productos por nombre'
GO

CREATE FUNCTION f_productsByNameLike (@name NVARCHAR(50))
	RETURNS TABLE
AS
RETURN(
	SELECT *
	FROM clients
	WHERE name LIKE '%'+@name+'%'
);

GO

PRINT 'Creando funcion de pedidos por estado digitado'
GO

CREATE FUNCTION f_productsByState (@statu NVARCHAR(50))
	RETURNS TABLE
AS
RETURN(
	SELECT *
	FROM orders
	WHERE status = @statu
);

GO

-- ------------------------------------------------------------------------ --
--                          CREACION DE TRIGGERS                            --
-- ------------------------------------------------------------------------ --

PRINT 'Creando Triggers'
GO

PRINT 'Creando Trigger de no borrado de productos con cantidad mayor que 0'
GO

-- No borrar productos con stock
CREATE TRIGGER dis_productDelete
ON products
FOR DELETE
AS
BEGIN
	DECLARE @amount INT

	SET @amount = (SELECT amount FROM deleted)

	IF (@amount > 0)
	BEGIN
		RAISERROR('Error... No se puede borrar un producto con cantidad mayor que 0', 16, 1)
		ROLLBACK TRANSACTION
	END

END

GO

PRINT 'Creando Trigger de no borrado de materiales con cantidad mayor que 0'
GO

-- No borrar materiales con stock
CREATE TRIGGER dis_materialDelete
ON materials
FOR DELETE
AS
BEGIN
	DECLARE @amount INT

	SET @amount = (SELECT amount FROM deleted)

	IF (@amount > 0)
	BEGIN
		RAISERROR('Error... No se puede borrar un material con cantidad mayor que 0', 16, 1)
		ROLLBACK TRANSACTION
	END

END

GO

PRINT 'Creando Trigger de no borrado de categorias con productos relacionados'
GO

-- No borrar categorias que tengan relaciones con productos
CREATE TRIGGER dis_deleteCategoryRelation
ON categories
INSTEAD OF DELETE
AS
BEGIN
	DECLARE @id BIGINT

	SELECT @id = id FROM deleted

	IF ((SELECT COUNT(categoryId)
		 FROM products
		 WHERE categoryId = @id) > 0)
	BEGIN
		RAISERROR('Error... No se puede borrar una categoria que tenga productos asociados', 16, 1)
		ROLLBACK TRANSACTION
	END

END

GO

/*
PRINT 'Creando Trigger de no borrado de productos con ordenes relacionadas'
GO

-- No borrar productos que tengan relaciones con ventas
drop TRIGGER dis_deleteProductRelation
ON products
INSTEAD OF DELETE
AS
BEGIN
	DECLARE @id BIGINT

	SELECT @id = id FROM deleted

	IF ((SELECT COUNT(orderId)
		 FROM product_order
		 WHERE id = @id) > 0)
	BEGIN
		RAISERROR('Error... No se puede borrar un producto que tenga pedidos asociados', 16, 1)
		ROLLBACK TRANSACTION
	END

END

GO
*/

PRINT 'Creando Trigger de no borrado de materiales con productos relacionados'
GO

-- No borrar materiales que tengan relaciones con productos
CREATE TRIGGER dis_deleteMaterialRelation
ON materials
INSTEAD OF DELETE
AS
BEGIN
	DECLARE @quantity INT, @id BIGINT

	SELECT @id = id FROM deleted

    IF ((SELECT COUNT(materialId)
		 FROM product_material
		 WHERE id = @id) > 0)
	BEGIN
		RAISERROR('Error... No se puede borrar un material que tenga productos asociados', 16, 1)
		ROLLBACK TRANSACTION
	END

END

GO

PRINT 'Creando Trigger de resta de materiales al añadir x cantidad de productos'
GO

-- Restar materiales al insertar producto
CREATE TRIGGER dis_productAddAmount
ON products
AFTER UPDATE
AS
BEGIN
	DECLARE @productId BIGINT, @materialId BIGINT, @updated_at DATETIME, @amount INT, @fail int,
            @materialName NVARCHAR(255), @materialAmount INT, @actualAmount INT, @lessAmount INT
    SELECT @productId = id, @amount = amount, @updated_at = updated_at FROM inserted

    BEGIN TRANSACTION;

    DECLARE cProducts CURSOR FOR
    -- Consulta materiales que se relacionan con el producto a restar
    SELECT materialId, amount
    FROM product_material
    WHERE productId = @productId

    -- Cursor
	OPEN cProducts
    FETCH cProducts INTO @materialId, @materialAmount
    WHILE(@@FETCH_STATUS = 0)
    BEGIN

        -- Monto Material y Monto a Restar
        SET @actualAmount = (SELECT amount FROM materials WHERE id = @materialId)
        SET @lessAmount = @amount * @materialAmount

        -- Si lo que tengo es mayor a lo que se necesita para crear un producto,
        -- ejecuto el P.A. para restar materiales
        IF(@actualAmount >= @lessAmount)
        BEGIN
            EXEC pa_updateLessAmountMaterial @materialId, @lessAmount, @updated_at
        END
        -- Si en algún momento no es suficiente, saco el nombre del material,
        -- y vamos al CANCEL
        ELSE
        BEGIN
            SET @materialName = (SELECT name FROM materials WHERE id = @materialId)
			SELECT @fail = 1;
            GOTO CANCEL
        END

        FETCH cProducts INTO @materialId, @materialAmount
    END

    -- En caso de que todo sucediera correctamente, liberar memoria y COMMIT
    CLOSE cProducts
    DEALLOCATE cProducts
    COMMIT TRANSACTION;
	SELECT @fail = 0;

    -- En caso de redirigir aquí, Mostramos material insuficiente y Rollback
	-- fail es para que no entre en caso de éxito
    CANCEL:
	IF (@fail = 1)
	BEGIN
		SELECT 'No tenemos *' + @materialName + '* suficientes para crear el producto' AS status;
		CLOSE cProducts
		DEALLOCATE cProducts
		ROLLBACK TRANSACTION;
	END
END

GO


/*
PRINT 'Creando Trigger de resta de productos al realizar ordenes'
GO

-- Restar productos al realizar pedido
CREATE TRIGGER dis_orderInsert
ON orders
FOR INSERT
AS
BEGIN
	DECLARE @productId BIGINT, @orderId BIGINT, @updated_at DATETIME, @amount INT, @fail int,
            @productName NVARCHAR(255), @productAmount INT, @actualAmount INT, @lessAmount INT
    SELECT @orderId = id, @updated_at = updated_at FROM inserted

    BEGIN TRANSACTION;

    DECLARE cOrder CURSOR FOR
    -- Consulta productos que se relacionan con la orden a realizar
    SELECT productId, amount
    FROM product_order
    WHERE productId = @productId

    -- Cursor
	OPEN cOrder
    FETCH cOrder INTO @productId, @productAmount
    WHILE(@@FETCH_STATUS = 0)
    BEGIN

        -- Monto Productos y Monto a Restar
        SET @actualAmount = (SELECT amount FROM products WHERE id = @productId)
        SET @lessAmount = @amount * @productAmount

        -- Si lo que tengo es mayor a lo que se necesita para crear la order,
        -- ejecuto el P.A. para restar productos
        IF(@actualAmount >= @lessAmount)
        BEGIN
            EXEC pa_updateLessAmountProduct @productId, @lessAmount, @updated_at
        END
        -- Si en algún momento no es suficiente, saco el nombre del producto,
        -- y vamos al CANCEL
        ELSE
        BEGIN
            SET @productName = (SELECT name FROM products WHERE id = @productId)
			SELECT @fail = 1;
            GOTO CANCEL
        END

        FETCH cOrder INTO @productId, @productAmount
    END

    -- En caso de que todo sucediera correctamente, liberar memoria y COMMIT
    CLOSE cOrder
    DEALLOCATE cOrder
    COMMIT TRANSACTION;
	SELECT @fail = 0;

    -- En caso de redirigir aquí, Mostramos material insuficiente y Rollback
	-- fail es para que no entre en caso de éxito
    CANCEL:
	IF (@fail = 1)
	BEGIN
		SELECT 'No tenemos *' + @productName + '* suficientes para crear la orden' AS status;
		CLOSE cOrder
		DEALLOCATE cOrder
		ROLLBACK TRANSACTION;
	END
END

GO

PRINT 'Creando Trigger de suma de productos al realizar devoluciones'
GO

*/
-- Sumar productos al insertar devolucion
-- CREO QUE EL TRIGGER DE PRODUCT ORDERS ESTÁ MALO, REVISAR CON LAS PRUEBAS ANTES DE HACER ESTE
-- DEBEN SALTAR EN LA TABLA INTERMEDIA, NO EN LA TABLA PEDIDOS / DEVOLUCIONES
/*
CREATE TRIGGER dis_refundInsert
ON product_refund
FOR INSERT
AS
BEGIN
	DECLARE @refundId BIGINT, @productId BIGINT, @orderId BIGINT, @amount INT, @fail int,
            @productName NVARCHAR(255), @productAmount INT, @actualAmount INT, @lessAmount INT

    SELECT @refundId = refundId, @amount = amount FROM inserted
    SELECT @orderId = (SELECT orderId FROM refunds WHERE id = @refundId)

    BEGIN TRANSACTION;

    DECLARE cRefund CURSOR FOR
    -- Consulta productos que se relacionan con la orden a realizar
    SELECT productId, amount
    FROM product_order
    WHERE orderId = @orderId

    -- Cursor
	OPEN cRefund
    FETCH cRefund INTO @productId, @productOrderAmount
    WHILE(@@FETCH_STATUS = 0)
    BEGIN

        -- Monto Productos y Monto a Restar
        SET @actualAmount = (SELECT amount FROM products WHERE id = @productId)
        SET @lessAmount = @amount * @productAmount

        -- Si lo que devuelvo es menor o igual a lo que se vendió,
        -- ejecuto el P.A. para aumentar productos
        IF(@actualAmount >= @lessAmount)
        BEGIN
            EXEC pa_updateLessAmountProduct @productId, @lessAmount, @updated_at
        END
        -- Si en algún momento no es suficiente, saco el nombre del producto,
        -- y vamos al CANCEL
        ELSE
        BEGIN
            SET @productName = (SELECT name FROM products WHERE id = @productId)
			SELECT @fail = 1;
            GOTO CANCEL
        END

        FETCH cRefund INTO @productId, @productAmount
    END

    -- En caso de que todo sucediera correctamente, liberar memoria y COMMIT
    CLOSE cRefund
    DEALLOCATE cRefund
    COMMIT TRANSACTION;
	SELECT @fail = 0;

    -- En caso de redirigir aquí, Mostramos material insuficiente y Rollback
	-- fail es para que no entre en caso de éxito
    CANCEL:
	IF (@fail = 1)
	BEGIN
		SELECT 'No tenemos *' + @productName + '* suficientes para crear la orden' AS status;
		CLOSE cOrder
		DEALLOCATE cOrder
		ROLLBACK TRANSACTION;
	END
END

GO
*/


-- ESTE TRIGGER DEBERIA SER EN product_refund, EN CADA INSERCION QUE VALIDE SI EL AMOUNT ES VALIDO
-- CONSULTA: EMPEZANDO DESDE refund (ID) JOIN orders (ORDERID) JOIN product_order (SACAR AMOUNT Y PRODUCTID)
-- SI AMOUNT(product_refund) ES <= A AMOUNT(product_refund) INSERTAR
-- ELSE CANCEL

PRINT 'Triggers de Auditoria - Pedidos'
GO

CREATE TRIGGER Audit_insert_orders
ON orders
FOR INSERT
AS
    DECLARE @userAudit BIGINT, @orderId BIGINT, @shippingNew BIGINT,
    @action NVARCHAR(255), @statusNew NVARCHAR(255), @created_at DATETIME

    SELECT @orderId = id, @shippingNew = ShippingId,
    @statusNew = status, @created_at = created_at FROM inserted
    SET @userAudit = (SELECT system_user)
    SET @action = 'Insertar'

    INSERT INTO audit_orders(userAudit, orderId, shippingNew, action, statusNew, created_at)
    VALUES (@userAudit, @orderId, @shippingNew, @action, @statusNew, @created_at)
GO

CREATE TRIGGER Audit_update_orders
ON orders
FOR UPDATE
AS
    DECLARE @userAudit BIGINT, @orderId BIGINT, @shippingOld BIGINT, @shippingNew BIGINT,
    @action NVARCHAR(255), @statusOld NVARCHAR(255), @statusNew NVARCHAR(255), @created_at DATETIME

    SELECT @orderId = id, @shippingNew = ShippingId,
    @statusNew = status, @created_at = created_at FROM inserted

    SELECT @shippingOld = ShippingId, @statusOld = status FROM deleted

    SET @userAudit = (SELECT system_user)
    SET @action = 'Actualizar'

    INSERT INTO audit_orders(userAudit, orderId, shippingOld, shippingNew, action, statusOld, statusNew, created_at)
    VALUES (@userAudit, @orderId, @shippingOld, @shippingNew, @action, @statusOld, @statusNew, @created_at)
GO

PRINT 'Triggers de Auditoria - Colaboradores'
GO

CREATE TRIGGER Audit_insert_collaborators
ON collaborators
FOR INSERT
AS
    DECLARE @userAudit NVARCHAR(50), @collaboratorEditedId UNIQUEIDENTIFIER,
    @action NVARCHAR(255), @usernameNew NVARCHAR(255),
    @emailNew NVARCHAR(255), @created_at DATETIME

    SELECT @collaboratorEditedId = id, @usernameNew = username,
    @emailNew = email, @created_at = created_at FROM inserted
    SET @userAudit = (SELECT system_user)
    SET @action = 'Insertar'

    INSERT INTO audit_collaborators(userAudit, collaboratorEditedId, action, usernameNew, emailNew, created_at)
    VALUES (@userAudit, @collaboratorEditedId, @action, @usernameNew, @emailNew, @created_at)
GO

CREATE TRIGGER Audit_update_collaborators
ON collaborators
FOR UPDATE
AS
    DECLARE @userAudit NVARCHAR(50), @collaboratorEditedId UNIQUEIDENTIFIER,
    @action NVARCHAR(255), @usernameOld NVARCHAR(255),
	@usernameNew NVARCHAR(255),	@emailOld NVARCHAR(255),
    @emailNew NVARCHAR(255), @created_at DATETIME

    SELECT @collaboratorEditedId = id, @collaboratorEditedId = id,
    @emailNew = email, @created_at = created_at FROM inserted

    SELECT @usernameOld = username, @emailOld = email FROM deleted

    SET @userAudit = (SELECT system_user)
    SET @action = 'Actualizar'

    INSERT INTO audit_collaborators(userAudit, collaboratorEditedId, action, usernameOld, usernameNew,
    emailOld, emailNew, created_at)
    VALUES (@userAudit, @collaboratorEditedId, @action, @usernameOld, @usernameNew, @emailOld, @emailNew, @created_at)
GO
