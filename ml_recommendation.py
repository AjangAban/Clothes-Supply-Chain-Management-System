import pandas as pd
from prophet import Prophet
from sklearn.cluster import KMeans
import matplotlib.pyplot as plt
import os

# Paths
EXPORT_DIR = 'storage/app/exports/'
SALES_FILE = os.path.join(EXPORT_DIR, 'sales_data.csv')
CUSTOMER_FILE = os.path.join(EXPORT_DIR, 'customer_data.csv')
SEGMENTS_FILE = os.path.join(EXPORT_DIR, 'customer_segments.csv')
RECOMMENDATIONS_FILE = os.path.join(EXPORT_DIR, 'segment_recommendations.csv')

# --- 1. Demand Forecasting ---
print('Loading sales data...')
sales = pd.read_csv(SALES_FILE)
sales_agg = sales.groupby('date').agg({'quantity_sold': 'sum'}).reset_index()
sales_agg.columns = ['ds', 'y']

print('Fitting Prophet model for demand forecasting...')
model = Prophet()
model.fit(sales_agg)
future = model.make_future_dataframe(periods=30)  # Predict next 30 days
forecast = model.predict(future)

# Plot forecast
fig1 = model.plot(forecast)
plt.title('Sales Forecast (Total)')
plt.savefig(os.path.join(EXPORT_DIR, 'sales_forecast.png'))
plt.close(fig1)

# --- 2. Customer Segmentation ---
print('Loading customer data...')
customers = pd.read_csv(CUSTOMER_FILE)
customers['purchase_date'] = pd.to_datetime(customers['purchase_date'])
snapshot_date = customers['purchase_date'].max() + pd.Timedelta(days=1)

# RFM features
rfm = customers.groupby('customer_id').agg({
    'purchase_date': lambda x: (snapshot_date - x.max()).days,
    'product_id': 'count',
    'amount': 'sum'
}).reset_index()
rfm.columns = ['customer_id', 'recency', 'frequency', 'monetary']

# Fill NA and scale
rfm = rfm.fillna(0)

print('Clustering customers (KMeans)...')
kmeans = KMeans(n_clusters=3, random_state=42)
rfm['segment'] = kmeans.fit_predict(rfm[['recency', 'frequency', 'monetary']])

# Save segments
rfm.to_csv(SEGMENTS_FILE, index=False)
print(f'Customer segments saved to {SEGMENTS_FILE}')

# --- 3. Personalization Recommendations ---
print('Generating recommendations per segment...')
customer_segments = customers.merge(rfm[['customer_id', 'segment']], on='customer_id')
top_products = customer_segments.groupby(['segment', 'product_id']).size().reset_index(name='count')
recommendations = top_products.sort_values(['segment', 'count'], ascending=[True, False]).groupby('segment').head(3)
recommendations.to_csv(RECOMMENDATIONS_FILE, index=False)
print(f'Recommendations saved to {RECOMMENDATIONS_FILE}')

print('Pipeline complete!') 