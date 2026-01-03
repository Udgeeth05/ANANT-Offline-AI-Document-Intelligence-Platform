# ANANT – Offline AI Document Intelligence Platform

ANANT is a ChatGPT-style offline AI dashboard built using PHP that allows users to upload documents (especially PDFs) and ask questions naturally in a conversational interface. The system intelligently answers either from the uploaded document or from general AI knowledge, without requiring the user to specify the source explicitly.

The entire platform works **offline**, ensuring privacy, security, and independence from cloud-based AI services.

---

## 🚀 Features

- ChatGPT-like conversational interface  
- Upload PDFs and ask questions naturally  
- Automatic document-based answering when a file is present  
- General AI responses when no document is uploaded  
- Fully offline AI using local models  
- Secure user authentication system  
- Real-time online/offline connectivity indicator  
- Chat history stored in database  
- Modern, professional dashboard UI  

---

## 🧠 How It Works

1. User logs in to the dashboard  
2. User optionally uploads a PDF or document  
3. XPDF extracts text locally from the uploaded file  
4. PHP builds an intelligent prompt using document context  
5. Ollama processes the prompt using a local LLM  
6. AI responds in a ChatGPT-style chat interface  

If no document is uploaded, the AI answers normally.

---

## 🛠️ Tech Stack

### Backend
- PHP (Core backend logic)
- MySQL (Database)
- PDO (Secure database queries)

### Frontend
- HTML
- CSS (Tailwind CSS)
- JavaScript (UI interactions, connectivity detection)

### AI & Document Processing
- Ollama (Local AI models – LLaMA 3.x)
- XPDF (Offline PDF text extraction, C/C++ utility)

### System
- XAMPP (Apache + PHP + MySQL)
- Windows (Local deployment)

---

## 📁 Project Structure

