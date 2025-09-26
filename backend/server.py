from fastapi import FastAPI, APIRouter, HTTPException
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field
from typing import List, Optional
import uuid
from datetime import datetime, timezone
import re

ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

# MongoDB connection
mongo_url = os.environ['MONGO_URL']
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ['DB_NAME']]

# Create the main app without a prefix
app = FastAPI()

# Create a router with the /api prefix
api_router = APIRouter(prefix="/api")

# Define Models
class VINReport(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    vin: str
    customer_name: str
    customer_email: str
    customer_phone: str
    report_provider: str  # "carfax" or "autocheck"
    plan_type: str  # "basic", "premium", "ultimate"
    payment_status: str = "pending"  # "pending", "completed", "failed"
    payment_id: Optional[str] = None
    total_amount: float
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))
    report_delivered: bool = False

class VINReportCreate(BaseModel):
    vin: str
    customer_name: str
    customer_email: str
    customer_phone: str
    report_provider: str
    plan_type: str
    total_amount: float

class ContactMessage(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    email: str
    message: str
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class ContactMessageCreate(BaseModel):
    name: str
    email: str
    message: str

# Utility functions
def validate_vin(vin: str) -> bool:
    """Validate VIN format (17 characters, alphanumeric, no I, O, Q)"""
    if len(vin) != 17:
        return False
    # VIN should not contain I, O, Q
    forbidden_chars = {'I', 'O', 'Q'}
    return vin.isalnum() and not any(char in forbidden_chars for char in vin.upper())

def calculate_price(plan_type: str, provider: str) -> float:
    """Calculate price based on plan and provider"""
    base_prices = {
        "basic": 29.99,
        "premium": 39.99,
        "ultimate": 49.99
    }
    return base_prices.get(plan_type, 39.99)

# API Routes
@api_router.get("/")
async def root():
    return {"message": "VinReporting.com API - Premium Vehicle History Reports"}

@api_router.post("/validate-vin")
async def validate_vin_endpoint(data: dict):
    vin = data.get("vin", "").upper().strip()
    is_valid = validate_vin(vin)
    return {"valid": is_valid, "vin": vin}

@api_router.post("/orders", response_model=VINReport)
async def create_order(order_data: VINReportCreate):
    # Validate VIN
    if not validate_vin(order_data.vin):
        raise HTTPException(status_code=400, detail="Invalid VIN format")
    
    # Create order
    order_dict = order_data.dict()
    order_obj = VINReport(**order_dict)
    
    # Insert into database
    result = await db.vin_reports.insert_one(order_obj.dict())
    
    return order_obj

@api_router.get("/orders", response_model=List[VINReport])
async def get_orders():
    orders = await db.vin_reports.find().to_list(1000)
    return [VINReport(**order) for order in orders]

@api_router.get("/orders/{order_id}", response_model=VINReport)
async def get_order(order_id: str):
    order = await db.vin_reports.find_one({"id": order_id})
    if not order:
        raise HTTPException(status_code=404, detail="Order not found")
    return VINReport(**order)

@api_router.put("/orders/{order_id}/payment")
async def update_payment_status(order_id: str, payment_data: dict):
    update_data = {
        "payment_status": payment_data.get("status", "completed"),
        "payment_id": payment_data.get("payment_id")
    }
    
    result = await db.vin_reports.update_one(
        {"id": order_id},
        {"$set": update_data}
    )
    
    if result.matched_count == 0:
        raise HTTPException(status_code=404, detail="Order not found")
    
    return {"success": True, "message": "Payment status updated"}

@api_router.post("/contact", response_model=ContactMessage)
async def submit_contact_message(message_data: ContactMessageCreate):
    message_dict = message_data.dict()
    message_obj = ContactMessage(**message_dict)
    
    # Insert into database
    await db.contact_messages.insert_one(message_obj.dict())
    
    return message_obj

@api_router.get("/pricing")
async def get_pricing():
    return {
        "plans": {
            "basic": {
                "name": "Basic Report",
                "price": 29.99,
                "features": ["Accident History", "Title Information", "Basic Records"]
            },
            "premium": {
                "name": "Premium Report",
                "price": 39.99,
                "features": ["Everything in Basic", "Service Records", "Previous Owners", "Market Value"]
            },
            "ultimate": {
                "name": "Ultimate Report", 
                "price": 49.99,
                "features": ["Everything in Premium", "Detailed Photos", "Recalls & Safety", "Comprehensive Timeline"]
            }
        }
    }

# Include the router in the main app
app.include_router(api_router)

app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=os.environ.get('CORS_ORIGINS', '*').split(','),
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()