# 1. Inicializa el repositorio Git (solo si no lo has hecho antes)
git init

# 2. Añade todos tus archivos al área de preparación
git add .

# 3. Haz tu primer commit
git commit -m "Primer commit: Página inicial HTML, CSS e imagen"

# 4. Cambia el nombre de la rama a 'main' si tu repositorio remoto usa 'main'
# (Esto es común en repositorios nuevos de GitHub)
git branch -M main

# 5. Conecta tu repositorio local con tu repositorio en GitHub
# Sustituye 'yourusername' y 'your-repo-name' con los tuyos
git remote add origin https://github.com/yourusername/your-repo-name.git

# 6. Sube tus archivos a GitHub
git push -u origin main 
