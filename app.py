from fastapi import FastAPI, Depends, HTTPException, Request
from fastapi.staticfiles import StaticFiles
from fastapi.templating import Jinja2Templates
from fastapi.security import HTTPBasic, HTTPBasicCredentials

import config
from api.submit import router as submit_router
from api.stats import router as stats_router
from api.export import router as export_router

app = FastAPI(title="Encuesta EmprendeISTG", version="1.0")

app.mount("/static", StaticFiles(directory="static"), name="static")
templates = Jinja2Templates(directory="templates")

app.include_router(submit_router)
app.include_router(stats_router)
app.include_router(export_router)

security = HTTPBasic()


def verificar_auth(creds: HTTPBasicCredentials = Depends(security)):
    if creds.username != config.ADMIN_USER or creds.password != config.ADMIN_PASS:
        raise HTTPException(status_code=401, detail="No autorizado", headers={"WWW-Authenticate": "Basic"})
    return creds


@app.get("/")
async def index(request: Request):
    return templates.TemplateResponse("encuesta.html", {"request": request})


@app.get("/gracias")
async def gracias(request: Request):
    return templates.TemplateResponse("gracias.html", {"request": request})


@app.get("/dashboard")
async def dashboard(request: Request, _=Depends(verificar_auth)):
    return templates.TemplateResponse("dashboard.html", {"request": request})
