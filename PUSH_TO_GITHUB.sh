#!/bin/bash
# Run this script after filling in your details below

GITHUB_USERNAME="YOUR_USERNAME_HERE"
REPO_NAME="odyssey-pms"
GITHUB_TOKEN="YOUR_TOKEN_HERE"

echo "Setting remote..."
git remote add origin https://${GITHUB_USERNAME}:${GITHUB_TOKEN}@github.com/${GITHUB_USERNAME}/${REPO_NAME}.git

echo "Pushing to GitHub..."
git push -u origin main

echo ""
echo "✅ Done! Repo is live at: https://github.com/${GITHUB_USERNAME}/${REPO_NAME}"
echo ""
echo "Next steps:"
echo "1. Go to railway.app and sign in with GitHub"
echo "2. Click New Project → Deploy from GitHub repo → select ${REPO_NAME}"
echo "3. Add a MySQL database service to the project"
echo "4. Railway auto-wires MYSQLHOST, MYSQLPORT, etc. to your app"
echo "5. Set APP_URL to your Railway URL in Variables tab"
echo "6. Deploy — migrations + seeders run automatically"
