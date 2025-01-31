import cv2
import pytesseract

# Load image, convert to grayscale, and apply thresholding
image = cv2.imread('C:/xampp/htdocs/kestrel/elements/images/front.JPG')
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
_, thresh = cv2.threshold(gray, 150, 255, cv2.THRESH_BINARY)

# Run OCR on processed image
text = pytesseract.image_to_string(thresh)
print(text)
