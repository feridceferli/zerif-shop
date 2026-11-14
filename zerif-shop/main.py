import os
import logging
import asyncio
from aiohttp import web
from dotenv import load_dotenv

load_dotenv()

logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

PORT = int(os.environ.get("PORT", 8080))
NOTIFY_GROUP_ID = os.environ.get("NOTIFY_GROUP_ID")

async def handle_index(request):
    return web.FileResponse('./static/index.html')

async def handle_webhook(request):
    try:
        data = await request.json()
        logger.info(f"Received webhook data: {data}")
        # Process incoming SMS / webhook data here
        return web.json_response({"status": "success"})
    except Exception as e:
        logger.error(f"Webhook error: {e}")
        return web.json_response({"status": "error", "message": str(e)}, status=400)

async def init_app():
    app = web.Application()
    app.router.add_get('/', handle_index)
    app.router.add_static('/static/', path='./static', name='static')
    app.router.add_post('/webhook', handle_webhook)
    return app

if __name__ == '__main__':
    logger.info("Starting IvaSMS Web Server & Automation Dashboard...")
    app = asyncio.run(init_app())
    web.run_app(app, port=PORT)
