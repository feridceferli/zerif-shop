# Telegram Bot UI & Keyboards for IvaSMS Mini App
from aiogram.types import InlineKeyboardMarkup, InlineKeyboardButton, WebAppInfo

def get_main_menu_keyboard(webapp_url: str):
    keyboard = InlineKeyboardMarkup(
        inline_keyboard=[
            [
                InlineKeyboardButton(text="📱 Open IvaSMS Mini App", web_app=WebAppInfo(url=webapp_url))
            ],
            [
                InlineKeyboardButton(text="💳 Balance Topup", callback_data="topup"),
                InlineKeyboardButton(text="📊 My Services", callback_data="services")
            ],
            [
                InlineKeyboardButton(text="👥 Referral Program", callback_data="referral")
            ]
        ]
    )
    return keyboard
