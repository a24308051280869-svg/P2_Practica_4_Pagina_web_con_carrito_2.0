-- =====================================================
--  BASE DE DATOS DE KEVLAR (versión 4)
--  Para usarla: phpMyAdmin -> pestaña "Importar"
--  -> elegir este archivo -> botón "Importar"
--  Tiene la tabla de usuarios (formulario), la de productos, la del carrito y las de pedidos
--  (10 de Trajes, 10 de Armas y 10 de Accesorios).
--  Si lo importo otra vez, los productos se vuelven a crear
--  desde cero y los usuarios registrados NO se borran
--  (el carrito sí se vacía, porque los productos se vuelven a crear).
--  Los pedidos ya hechos tampoco se borran.
-- =====================================================

-- Crea la base de datos (si ya existe, no pasa nada)
CREATE DATABASE IF NOT EXISTS kevlar_V4 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Me meto a esa base
USE kevlar_V4;

-- Tabla USUARIOS: aquí se guardan las personas que se registran
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,   -- número que se pone solo (1, 2, 3...)
    nombre     VARCHAR(60)  NOT NULL,
    apellido   VARCHAR(60)  NOT NULL,
    correo     VARCHAR(120) NOT NULL UNIQUE,     -- no se puede repetir
    telefono   VARCHAR(20),                      -- puede quedar vacío
    contrasena VARCHAR(255) NOT NULL,            -- se guarda tal cual, sin cifrar
    fecha      TIMESTAMP DEFAULT CURRENT_TIMESTAMP   -- cuándo se registró
);

-- Tabla PRODUCTOS: todo lo que vendemos
-- (la borro y la vuelvo a crear para que no se repitan los productos)
-- (primero borro el carrito porque depende de productos)
DROP TABLE IF EXISTS carrito;
DROP TABLE IF EXISTS productos;
CREATE TABLE productos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT         NOT NULL,
    categoria   VARCHAR(30)  NOT NULL,           -- Trajes, Armas o Accesorios
    heroe       VARCHAR(30)  NOT NULL,
    precio      DECIMAL(10,2) NOT NULL,
    stock       INT NOT NULL,                    -- cuántos hay disponibles
    imagen      VARCHAR(150)                     -- ruta de la foto
);

-- Los productos que ya vienen en la tienda
INSERT INTO productos (nombre, descripcion, categoria, heroe, precio, stock, imagen) VALUES
-- ================= TRAJES (10) =================
('Traje Daredevil Rojo', 'Traje completo estilo vigilante rojo, tela elástica con refuerzos acolchados. Incluye capucha.', 'Trajes', 'Daredevil', 3499, 12, 'img/productos/traje-daredevil-rojo.jpg'),
('Traje Daredevil Negro Táctico', 'Versión oscura con paneles reforzados, ideal para cosplay y coleccionistas.', 'Trajes', 'Daredevil', 3899, 8, 'img/productos/traje-daredevil-negro.jpg'),
('Traje Daredevil Armadura Blindada', 'Traje con placas rígidas de espuma EVA en pecho y hombros, acabado rojo oscuro.', 'Trajes', 'Daredevil', 4299, 5, 'img/productos/traje-daredevil-armadura.jpg'),
('Traje Spider-Man Clásico', 'Traje de cuerpo completo con textura de red impresa y máscara con lentes removibles.', 'Trajes', 'Spider-Man', 3299, 15, 'img/productos/traje-spiderman-clasico.jpg'),
('Traje Spider-Man Edición Negra', 'Edición especial en negro y rojo con acabado mate.', 'Trajes', 'Spider-Man', 3799, 7, 'img/productos/traje-spiderman-negro.jpg'),
('Traje Spider-Man Iron Spider', 'Traje rojo y dorado con detalles metálicos impresos y patas decorativas para la espalda.', 'Trajes', 'Spider-Man', 4499, 4, 'img/productos/traje-spiderman-iron.jpg'),
('Traje Spider-Man Infantil', 'Traje de cuerpo completo en tallas infantiles, tela suave y cierre trasero.', 'Trajes', 'Spider-Man', 1999, 22, 'img/productos/traje-spiderman-infantil.jpg'),
('Traje Punisher Táctico', 'Chamarra y pantalón negros con calavera blanca estampada en el pecho.', 'Trajes', 'Punisher', 3599, 9, 'img/productos/traje-punisher.jpg'),
('Traje Black Panther', 'Traje negro con líneas plateadas texturizadas y capucha con orejas.', 'Trajes', 'Black Panther', 3999, 6, 'img/productos/traje-blackpanther.jpg'),
('Traje Vigilante Urbano Rojo y Negro', 'Traje original sin licencia, tela elástica bicolor pensado para cosplay libre.', 'Trajes', 'Universal', 2899, 14, 'img/productos/traje-vigilante.jpg'),

-- ================= ARMAS DE UTILERÍA (10) =================
('Bastón de Combate (réplica)', 'Réplica de utilería en dos piezas con cuerda decorativa. No apto para combate real.', 'Armas', 'Daredevil', 1299, 20, 'img/productos/baston-combate.jpg'),
('Bastón Doble de Entrenamiento', 'Par de bastones de espuma rígida para sesiones de foto y desfiles.', 'Armas', 'Daredevil', 1599, 11, 'img/productos/baston-doble.jpg'),
('Cuerda con Gancho (réplica)', 'Cuerda decorativa de 2 m con gancho de plástico. Solo utilería.', 'Armas', 'Daredevil', 749, 18, 'img/productos/cuerda-gancho.jpg'),
('Lanzarredes de Muñeca (réplica)', 'Par de lanzadores de utilería con correa ajustable. Sin mecanismo de disparo.', 'Armas', 'Spider-Man', 899, 30, 'img/productos/lanzarredes.jpg'),
('Lanzarredes con Luz LED', 'Lanzador de utilería con luz LED azul en la muñequera. Incluye pilas.', 'Armas', 'Spider-Man', 1199, 16, 'img/productos/lanzarredes-led.jpg'),
('Pistola Punisher (réplica de espuma)', 'Réplica de espuma suave tamaño real, sin funcionamiento. Para exhibición.', 'Armas', 'Punisher', 999, 13, 'img/productos/pistola-punisher.jpg'),
('Rifle Táctico de Utilería', 'Rifle de espuma EVA pintado a mano. Solo para cosplay, nunca para uso real.', 'Armas', 'Punisher', 1799, 7, 'img/productos/rifle-tactico.jpg'),
('Garras de Vibranium (réplica)', 'Par de garras plásticas plateadas con guantes ajustables.', 'Armas', 'Black Panther', 1099, 12, 'img/productos/garras-vibranium.jpg'),
('Lanza Ceremonial (réplica)', 'Lanza de utilería de 1.5 m, desarmable en tres piezas.', 'Armas', 'Black Panther', 1399, 9, 'img/productos/lanza-ceremonial.jpg'),
('Escudo de Utilería Táctico', 'Escudo circular de espuma y tela, con correas internas. Sin uso real.', 'Armas', 'Universal', 1499, 10, 'img/productos/escudo-tactico.jpg'),

-- ================= ACCESORIOS (10) =================
('Máscara Daredevil', 'Máscara de tela rígida con ajuste trasero y detalles en relieve.', 'Accesorios', 'Daredevil', 699, 25, 'img/productos/mascara-daredevil.jpg'),
('Máscara Spider-Man', 'Máscara de tela elástica con lentes removibles y buena ventilación.', 'Accesorios', 'Spider-Man', 649, 28, 'img/productos/mascara-spiderman.jpg'),
('Máscara Punisher Calavera', 'Pasamontañas negro con calavera blanca estampada.', 'Accesorios', 'Punisher', 399, 35, 'img/productos/mascara-punisher.jpg'),
('Máscara Black Panther', 'Máscara de espuma moldeada con detalles plateados.', 'Accesorios', 'Black Panther', 799, 14, 'img/productos/mascara-blackpanther.jpg'),
('Guantes Tácticos Negros', 'Guantes reforzados con nudillos acolchados, compatibles con varios trajes.', 'Accesorios', 'Universal', 549, 40, 'img/productos/guantes-tacticos.jpg'),
('Cinturón Utilitario', 'Cinturón de cuero sintético con bolsas y hebilla metálica roja.', 'Accesorios', 'Universal', 799, 18, 'img/productos/cinturon-utilitario.jpg'),
('Botas Tácticas Negras', 'Botas de caña media con suela antiderrapante. Varias tallas.', 'Accesorios', 'Universal', 1499, 20, 'img/productos/botas-tacticas.jpg'),
('Mochila Táctica Roja', 'Mochila de 30 litros con compartimentos y correas reforzadas.', 'Accesorios', 'Universal', 999, 17, 'img/productos/mochila-tactica.jpg'),
('Rodilleras y Coderas Tácticas', 'Set de protecciones acolchadas para sesiones de foto y eventos.', 'Accesorios', 'Universal', 649, 24, 'img/productos/rodilleras.jpg'),
('Gabardina Vigilante', 'Gabardina larga negra con forro rojo, ideal para complementar un cosplay.', 'Accesorios', 'Universal', 1899, 8, 'img/productos/gabardina-vigilante.jpg');

-- Tabla CARRITO: lo que cada persona tiene en su carrito
-- Una fila = un producto de una persona (con cuántas piezas lleva)
CREATE TABLE carrito (
    id_usuario  INT NOT NULL,                    -- de quién es el carrito
    id_producto INT NOT NULL,                    -- qué producto es
    cantidad    INT NOT NULL DEFAULT 1,          -- cuántas piezas
    PRIMARY KEY (id_usuario, id_producto),       -- el mismo producto no se repite en un carrito
    FOREIGN KEY (id_usuario)  REFERENCES usuarios(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla PEDIDOS: cada compra que se hace desde el carrito
-- (no la borro al importar para no perder los pedidos que ya hay)
CREATE TABLE IF NOT EXISTS pedidos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario  INT NOT NULL,                    -- quién hizo la compra (su cuenta)
    direccion   VARCHAR(200) NOT NULL,           -- a dónde se manda
    metodo_pago VARCHAR(30)  NOT NULL,           -- Tarjeta o Código promocional
    total       DECIMAL(10,2) NOT NULL,
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- cuándo se hizo
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla PEDIDO_PRODUCTOS: los productos de cada pedido
-- Guardo el nombre y el precio de cuando se compró, por si luego cambian en productos
CREATE TABLE IF NOT EXISTS pedido_productos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido   INT NOT NULL,                    -- a qué pedido pertenece
    id_producto INT NOT NULL,
    nombre      VARCHAR(100) NOT NULL,
    precio      DECIMAL(10,2) NOT NULL,
    cantidad    INT NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE
);
