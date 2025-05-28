# K-SALUD – Plataforma Interna para Clínica Sanitaria

Este repositorio contiene el desarrollo completo de **K-SALUD**, una plataforma interna de gestión para una clínica sanitaria, desarrollada como parte del Trabajo Final de Ciclo (TFC) del CFGS en Administración de Sistemas Informáticos en Red (ASIR).

## Descripción

K-SALUD permite al personal sanitario y administrativo gestionar de manera eficiente citas médicas, historiales clínicos, pacientes y control de acceso según roles. Todo el sistema ha sido implementado de forma segura utilizando tecnologías libres, tanto en software como en infraestructura.

## Objetivos

- Desarrollar una infraestructura completa basada en servidores propios.
- Crear una plataforma web interna segura conectada a MongoDB.
- Gestionar roles y sesiones para diferentes tipos de usuarios (administrador, médico, recepcionista).
- Asegurar la protección de datos conforme al RGPD.
- Aplicar los conocimientos adquiridos en todas las asignaturas del ciclo ASIR.

## Tecnologías utilizadas

- **PHP** – Lógica de backend y sesiones.
- **HTML/CSS** – Diseño de la interfaz.
- **MongoDB** – Base de datos NoSQL.
- **Apache2 (XAMPP)** – Servidor web local.
- **Ubuntu Server** – Entorno de despliegue.
- **Windows Server** – Dominio interno para el acceso mediante GPO.
- **MongoDB Compass** – Administración visual de datos.
- **Packet Tracer** – Diseño de red.
- **ClickUp / Excel** – Diagrama de Gantt y planificación.
- **Git** – Control de versiones.
- **VSCode** – Editor principal de desarrollo.


## 🧪 Cómo probarlo

1. Clona el repositorio en tu entorno local.
2. Asegúrate de tener instalado **XAMPP**, con Apache y MongoDB en funcionamiento.
3. Coloca el proyecto dentro de `htdocs`.
4. Instala dependencias de Composer si es necesario.
5. Asegúrate de tener una base de datos en MongoDB con las colecciones:
   - `usuarios`
   - `pacientes`
   - `citas`
6. Accede a `http://localhost/Web/index.php` y prueba el sistema.

## 👤 Autores

**Namir Kubba Consuegra**  
2º ASIR – Universidad Alfonso X El Sabio

## 📄 Licencia

Este proyecto ha sido desarrollado como trabajo académico. Su código puede ser reutilizado para fines educativos o prototipos sin fines comerciales, mencionando al autor.

---

> Proyecto presentado como Trabajo Final de Ciclo (TFC) del CFGS en ASIR.
